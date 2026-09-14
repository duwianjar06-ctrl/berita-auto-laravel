#!/usr/bin/env bash
set -euo pipefail

: "${FTP_SERVER:?FTP_SERVER is required}"
: "${FTP_USERNAME:?FTP_USERNAME is required}"
: "${FTP_PASSWORD:?FTP_PASSWORD is required}"
: "${FTP_SERVER_DIR:?FTP_SERVER_DIR is required}"
: "${GITHUB_SHA:?GITHUB_SHA is required}"
: "${DEPLOY_MODE:=full}"

BASE="${FTP_SERVER_DIR%/}"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT
STAGE="$WORK/stage"
VERIFY_DIR="$WORK/verify"
mkdir -p "$STAGE" "$VERIFY_DIR"

lftp_common() {
    lftp -u "$FTP_USERNAME","$FTP_PASSWORD" -p 21 "ftp://$FTP_SERVER" <<EOF2
set cmd:fail-exit yes
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 8
set net:reconnect-interval-base 5
set net:reconnect-interval-max 30
set xfer:clobber true
set xfer:parallel 1
$*
bye
EOF2
}

remote_mkdir() {
    local remote="$1"
    lftp_common "mkdir \"$remote\"" 2>/dev/null || true
}

remote_get() {
    local remote="$1" local_file="$2"
    lftp_common "get \"$remote\" -o \"$local_file\""
}

remote_put() {
    local local_file="$1" remote_dir="$2"
    [[ -f "$local_file" ]] || { echo "Local upload file not found: $local_file" >&2; return 1; }
    [[ -n "$remote_dir" ]] || { echo "Remote upload directory is required" >&2; return 1; }
    lftp_common "put -O \"$remote_dir\" \"$local_file\""
}

printf '%s\n' '================================' 'SAFE FTPS DEPLOYMENT' '================================'
printf 'Target SHA: %s\n' "$GITHUB_SHA"
printf 'Remote base: %s\n' "$BASE"
printf 'Deployment mode: %s\n' "$DEPLOY_MODE"
printf '%s\n' 'Transfer mode: transfer-all; fresh overwrite; no resume/continuation'
printf '%s\n' 'Remote-only deletion: disabled for recovery'
printf '%s\n' 'Protected production-owned paths: .env, .env.*, storage/**, public/storage/**, public/.user.ini, public/php.ini, public/.well-known/**, .ftpquota, cgi-bin/**'
printf '%s\n' '================================'

# Build a deployment-only tree from tracked repository files. This prevents
# Composer/npm/build/test metadata from entering the FTP transfer.
git archive --format=tar "$GITHUB_SHA" | tar -xf - -C "$STAGE"
if [[ -d public/build ]]; then
    rm -rf "$STAGE/public/build"
    cp -a public/build "$STAGE/public/build"
fi
rm -rf "$STAGE/vendor" "$STAGE/node_modules" "$STAGE/tests" "$STAGE/.git" "$STAGE/.github" "$STAGE/storage" "$STAGE/public/storage"
find "$STAGE" -type f \( -name '.env' -o -name '.env.*' \) -delete

remote_mkdir "$BASE"
remote_mkdir "$BASE/.deploy"

# Recovery deliberately overwrites every staged source file, even when the
# remote size and timestamp appear unchanged. --delete is intentionally absent:
# hosting/runtime files not managed by Git must remain untouched.
lftp_common "mirror --reverse --transfer-all --parallel=1 --no-perms --verbose --exclude-glob 'vendor/**' --exclude-glob 'node_modules/**' --exclude-glob 'tests/**' --exclude-glob '.git/**' --exclude-glob '.github/**' --exclude-glob '.env' --exclude-glob '.env.*' --exclude-glob 'storage/**' --exclude-glob 'public/storage/**' --exclude-glob 'cgi-bin/**' --exclude-glob '.deploy/**' --exclude-glob '.phpunit.cache/**' --exclude-glob '.phpunit.result.cache' --exclude-glob '.idea/**' --exclude-glob '.vscode/**' --exclude-glob '.DS_Store' "$STAGE/" \"$BASE\""

VERIFY_FILES=(
    "routes/web.php"
    "app/Models/Article.php"
    "app/Http/Controllers/PublicController.php"
    "resources/views/home.blade.php"
)
for path in "${VERIFY_FILES[@]}"; do
    local_file="$VERIFY_DIR/$(basename "$path")"
    remote_get "$BASE/$path" "$local_file"
    source_sha="$(sha256sum "$path" | awk '{print $1}')"
    remote_sha="$(sha256sum "$local_file" | awk '{print $1}')"
    printf 'VERIFY %s source=%s remote=%s\n' "$path" "$source_sha" "$remote_sha"
    test "$source_sha" = "$remote_sha"
done

printf '%s\n' "$GITHUB_SHA" > "$WORK/current-sha"
remote_put "$WORK/current-sha" "$BASE/.deploy"
printf '%s\n' '================================' 'SAFE FTPS DEPLOY COMPLETE' '================================'

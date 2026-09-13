#!/usr/bin/env bash
set -euo pipefail

: "${FTP_SERVER:?FTP_SERVER is required}"
: "${FTP_USERNAME:?FTP_USERNAME is required}"
: "${FTP_PASSWORD:?FTP_PASSWORD is required}"
: "${FTP_SERVER_DIR:?FTP_SERVER_DIR is required}"
: "${GITHUB_SHA:?GITHUB_SHA is required}"

BASE="${FTP_SERVER_DIR%/}"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

lftp_common() {
    lftp -u "$FTP_USERNAME","$FTP_PASSWORD" -p 21 "ftp://$FTP_SERVER" <<EOF2
set cmd:fail-exit yes
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 30
set net:reconnect-interval-base 5
set net:reconnect-interval-max 30
set xfer:clobber true
$*
bye
EOF2
}

remote_mkdir() {
    local remote="$1"
    if lftp -u "$FTP_USERNAME","$FTP_PASSWORD" -p 21 "ftp://$FTP_SERVER" <<EOF2
set cmd:fail-exit yes
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 10
set net:reconnect-interval-base 5
set net:reconnect-interval-max 30
cd "$remote"
bye
EOF2
    then
        return 0
    fi
    lftp_common "mkdir \"$remote\""
}

remote_get() {
    local remote="$1" local_file="$2"
    lftp -u "$FTP_USERNAME","$FTP_PASSWORD" -p 21 "ftp://$FTP_SERVER" <<EOF2
set cmd:fail-exit yes
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 30
set net:reconnect-interval-base 5
set net:reconnect-interval-max 30
get "$remote" -o "$local_file"
bye
EOF2
}

remote_put() {
    local local_file="$1" remote_dir="$2"
    [[ -f "$local_file" ]] || { echo "Local upload file not found: $local_file" >&2; return 1; }
    [[ -n "$remote_dir" ]] || { echo "Remote upload directory is required" >&2; return 1; }
    lftp_common "put -O \"$remote_dir\" \"$local_file\""
}

printf '%s\n' '================================' 'FULL FTPS DEPLOY' '================================'
printf 'Target SHA: %s\n' "$GITHUB_SHA"
printf 'Remote base: %s\n' "$BASE"
printf '%s\n' 'Transfer mode: fresh overwrite; no resume/continuation'
printf '%s\n' '================================'

remote_mkdir "$BASE/.deploy"

# Every application file managed by this repository is sent again from the
# checked-out workspace. --delete removes stale application files, while the
# explicit excludes preserve production-only/runtime content.
lftp_common "mirror --reverse --delete --ignore-time --parallel=1 --no-perms --verbose --exclude-glob '.git/**' --exclude-glob '.github/**' --exclude-glob '.env' --exclude-glob '.env.*' --exclude-glob 'tests/**' --exclude-glob 'storage/**' --exclude-glob 'public/storage/**' --exclude-glob 'node_modules/**' --exclude-glob '.phpunit.cache/**' --exclude-glob '.phpunit.result.cache' --exclude-glob '.idea/**' --exclude-glob '.vscode/**' --exclude-glob '.DS_Store' --exclude-glob '.deploy/**' --exclude-glob 'cgi-bin/**' ./ \"$BASE\""

# A deployment is not successful until critical files downloaded from the
# actual FTP destination are byte-for-byte identical to the source checkout.
VERIFY_DIR="$WORK/verify"
mkdir -p "$VERIFY_DIR"
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

printf '%s\n' "$GITHUB_SHA" > "$WORK/deploy-complete"
remote_put "$WORK/deploy-complete" "$BASE/.deploy"
printf '%s\n' "$GITHUB_SHA" > "$WORK/current-sha"
remote_put "$WORK/current-sha" "$BASE/.deploy"

printf '%s\n' '================================' 'FULL FTPS DEPLOY COMPLETE' '================================'

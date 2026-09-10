#!/usr/bin/env bash
set -euo pipefail

: "${FTP_SERVER:?FTP_SERVER is required}"
: "${FTP_USERNAME:?FTP_USERNAME is required}"
: "${FTP_PASSWORD:?FTP_PASSWORD is required}"
: "${FTP_SERVER_DIR:?FTP_SERVER_DIR is required}"
: "${GITHUB_SHA:?GITHUB_SHA is required}"

BASE="${FTP_SERVER_DIR%/}"
REMOTE_STATE="$BASE/.deploy/current-sha"
REMOTE_MANIFEST="$BASE/.deploy/vendor-manifest.sha256"
WORK="$(mktemp -d)"
STAGE="$WORK/stage"
OLD_MANIFEST="$WORK/old-vendor-manifest.sha256"
NEW_MANIFEST="$WORK/vendor-manifest.sha256"
mkdir -p "$STAGE"
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
set mirror:use-pget-n 1
set xfer:clobber true
$*
bye
EOF2
}

remote_get() {
    local remote="$1" local_file="$2"
    if lftp -u "$FTP_USERNAME","$FTP_PASSWORD" -p 21 "ftp://$FTP_SERVER" <<EOF2
set cmd:fail-exit yes
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 10
set net:reconnect-interval-base 5
set net:reconnect-interval-max 30
get "$remote" -o "$local_file"
bye
EOF2
    then
        return 0
    fi
    rm -f "$local_file"
    return 1
}

remote_put() {
    local local_file="$1" remote_dir="$2"
    lftp_common "put \"$local_file\" -O \"$remote_dir\""
}

remote_rm() {
    local remote="$1"
    lftp_common "rm -f \"$remote\""
}

remote_mkdir() {
    local remote="$1"
    lftp_common "mkdir -p \"$remote\""
}

safe_path() {
    local p="$1"
    [[ -n "$p" ]] || return 1
    [[ "$p" != /* ]] || return 1
    [[ "$p" != *$'\n'* ]] || return 1
    [[ "$p" != *$'\r'* ]] || return 1
    [[ "$p" != *$'\t'* ]] || return 1
    [[ "$p" != *"../"* ]] || return 1
    [[ "$p" != *"/.."* ]] || return 1
    [[ "$p" != ".." ]] || return 1
}

protected_path() {
    local p="$1"
    [[ "$p" == ".env" || "$p" == .env.* || "$p" == storage/* || "$p" == public/storage || "$p" == public/storage/* || "$p" == .deploy/* || "$p" == .deploy || "$p" == cgi-bin || "$p" == cgi-bin/* || "$p" == .github/* || "$p" == .github || "$p" == tests/* || "$p" == tests || "$p" == node_modules/* || "$p" == node_modules || "$p" == .idea/* || "$p" == .vscode/* || "$p" == .DS_Store ]]
}

generate_vendor_manifest() {
    local output="$1"
    python3 - "$output" <<'PY'
import hashlib
import pathlib
import sys

root = pathlib.Path("vendor")
out = pathlib.Path(sys.argv[1])
rows = []
for path in sorted(root.rglob("*")):
    if path.is_file():
        rel = path.as_posix()
        if rel.startswith("/") or ".." in pathlib.PurePosixPath(rel).parts or any(c in rel for c in "\r\n\t"):
            raise SystemExit(f"Unsafe vendor path: {rel}")
        rows.append(f"{hashlib.sha256(path.read_bytes()).hexdigest()}\t{rel}")
out.write_text("\n".join(rows) + ("\n" if rows else ""), encoding="utf-8")
PY
}

printf '%s\n' '================================' 'SMART DEPLOY' '================================'
OLD_SHA=""
if remote_get "$REMOTE_STATE" "$WORK/current-sha"; then
    OLD_SHA="$(tr -d '\r\n' < "$WORK/current-sha")"
fi

if [[ -z "$OLD_SHA" ]]; then
    MODE="INITIAL"
else
    MODE="INCREMENTAL"
fi
printf 'Mode: %s\n' "$MODE"
printf 'Remote successful SHA: %s\n' "${OLD_SHA:-NONE}"
printf 'Target SHA: %s\n' "$GITHUB_SHA"
printf '%s\n' '================================'

if [[ "$MODE" == "INITIAL" ]]; then
    printf '[INITIAL] Resumable full-tree upload; existing remote files are preserved.\n'
    remote_mkdir "$BASE/.deploy"
    lftp_common "mirror --reverse --continue --ignore-time --parallel=2 --no-perms --verbose --exclude-glob '.git/**' --exclude-glob '.github/**' --exclude-glob '.env' --exclude-glob '.env.*' --exclude-glob 'tests/**' --exclude-glob 'storage/logs/**' --exclude-glob 'node_modules/**' --exclude-glob '.phpunit.cache/**' --exclude-glob '.phpunit.result.cache' --exclude-glob '.idea/**' --exclude-glob '.vscode/**' --exclude-glob '.DS_Store' --exclude-glob '.deploy/**' ./ "$BASE""
    generate_vendor_manifest "$NEW_MANIFEST"
else
    git fetch --no-tags --prune origin '+refs/heads/main:refs/remotes/origin/main'
    git cat-file -e "$OLD_SHA^{commit}"
    git cat-file -e "$GITHUB_SHA^{commit}"

    if git diff --quiet "$OLD_SHA..$GITHUB_SHA" -- composer.json composer.lock; then
        COMPOSER_CHANGED=NO
    else
        COMPOSER_CHANGED=YES
    fi

    CHANGED_COUNT=0
    DELETED_COUNT=0
    while IFS= read -r -d '' path; do
        safe_path "$path" || { echo "Unsafe repository path rejected" >&2; exit 1; }
        protected_path "$path" && continue
        [[ -f "$path" ]] || continue
        mkdir -p "$STAGE/$(dirname -- "$path")"
        cp -p -- "$path" "$STAGE/$path"
        CHANGED_COUNT=$((CHANGED_COUNT + 1))
    done < <(git diff --name-only -z --diff-filter=ACMR "$OLD_SHA..$GITHUB_SHA")

    while IFS= read -r -d '' path; do
        safe_path "$path" || { echo "Unsafe repository path rejected" >&2; exit 1; }
        protected_path "$path" && continue
        DELETED_COUNT=$((DELETED_COUNT + 1))
        printf '[DELETE] %s\n' "$path"
        remote_rm "$BASE/$path"
    done < <(git diff --name-only -z --diff-filter=D "$OLD_SHA..$GITHUB_SHA")

    if [[ "$COMPOSER_CHANGED" == "YES" ]]; then
        generate_vendor_manifest "$NEW_MANIFEST"
        if ! remote_get "$REMOTE_MANIFEST" "$OLD_MANIFEST"; then
            : > "$OLD_MANIFEST"
        fi

        python3 - "$OLD_MANIFEST" "$NEW_MANIFEST" "$STAGE" <<'PY'
import pathlib
import shutil
import sys

old_path, new_path, stage = map(pathlib.Path, sys.argv[1:])

def read_manifest(path):
    result = {}
    if not path.exists():
        return result
    for line in path.read_text(encoding="utf-8").splitlines():
        if not line.strip():
            continue
        digest, rel = line.split("\t", 1)
        pure = pathlib.PurePosixPath(rel)
        if rel.startswith("/") or ".." in pure.parts or any(c in rel for c in "\r\n\t"):
            raise SystemExit(f"Unsafe manifest path: {rel}")
        if not rel.startswith("vendor/"):
            raise SystemExit(f"Unexpected vendor manifest path: {rel}")
        result[rel] = digest
    return result

old = read_manifest(old_path)
new = read_manifest(new_path)
for rel, digest in sorted(new.items()):
    if old.get(rel) == digest:
        continue
    src = pathlib.Path(rel)
    dst = stage / rel
    dst.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(src, dst)

print(f"Vendor added: {len(set(new) - set(old))}")
print(f"Vendor changed: {sum(1 for key in set(new) & set(old) if new[key] != old[key])}")
print(f"Vendor removed: {len(set(old) - set(new))}")
with (stage / ".deploy-vendor-marker").open("w", encoding="utf-8") as marker:
    marker.write("ready\n")
PY

        while IFS= read -r path; do
            [[ -n "$path" ]] || continue
            safe_path "$path" || { echo "Unsafe vendor deletion path rejected" >&2; exit 1; }
            [[ "$path" == vendor/* ]] || { echo "Unexpected vendor deletion path" >&2; exit 1; }
            if ! grep -Fq $'\t'"$path" "$NEW_MANIFEST"; then
                printf '[DELETE] %s\n' "$path"
                remote_rm "$BASE/$path"
            fi
        done < <(python3 - "$OLD_MANIFEST" "$NEW_MANIFEST" <<'PY'
import pathlib, sys
old, new = [pathlib.Path(x) for x in sys.argv[1:]]
def paths(p):
    if not p.exists(): return set()
    return {line.split("\t", 1)[1] for line in p.read_text(encoding="utf-8").splitlines() if line.strip()}
for rel in sorted(paths(old) - paths(new)):
    print(rel)
PY
)
        rm -f "$STAGE/.deploy-vendor-marker"
        mkdir -p "$STAGE/.deploy"
        cp -- "$NEW_MANIFEST" "$STAGE/.deploy/vendor-manifest.sha256"
    fi

    printf '%s\n' '================================'
    printf 'Changed application files: %s\n' "$CHANGED_COUNT"
    printf 'Deleted application files: %s\n' "$DELETED_COUNT"
    printf 'Composer changed: %s\n' "$COMPOSER_CHANGED"
    printf 'Vendor update required: %s\n' "$COMPOSER_CHANGED"
    printf '%s\n' '================================'

    if find "$STAGE" -type f -print -quit | grep -q .; then
        lftp_common "mirror --reverse --continue --ignore-time --parallel=2 --no-perms --verbose --exclude-glob '.env*' --exclude-glob 'storage/logs/**' \"$STAGE/\" \"$BASE\""
    else
        printf 'No file uploads required.\n'
    fi
fi

remote_mkdir "$BASE/.deploy"
if [[ -f "$NEW_MANIFEST" ]]; then
    remote_put "$NEW_MANIFEST" "$BASE/.deploy"
fi
printf '%s\n' "$GITHUB_SHA" > "$WORK/deploy-complete"
remote_put "$WORK/deploy-complete" "$BASE/.deploy"
printf '%s\n' "$GITHUB_SHA" > "$WORK/current-sha"
remote_put "$WORK/current-sha" "$BASE/.deploy"

printf '%s\n' '================================' 'SMART DEPLOY COMPLETE' '================================'

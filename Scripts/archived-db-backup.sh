#!/bin/bash
set -euo pipefail
umask 077

backup_dir=${BACKUP_DIR:-/var/db_backup/archived}
database=${DATABASE_NAME:-archived}
mysql_config=${MYSQL_CONFIG:-/etc/archived-db-backup/mysql.cnf}
ssh_config=${SSH_CONFIG:-/etc/archived-db-backup/ssh_config}
remote_host=${BACKUP_SSH_HOST:-archived-backup}
remote_dir=${REMOTE_DIR:-/home/archived-db-backup}

log() {
    printf '[%s] %s\n' "$(date -Is)" "$*" >&2
}

if [[ ! $database =~ ^[[:alnum:]_]+$ || ! $remote_host =~ ^[[:alnum:]_][[:alnum:]_.-]*$ ||
      ! $remote_dir =~ ^/[[:alnum:]_][[:alnum:]_./-]*$ || /$remote_dir/ == */../* ]]; then
    log 'Invalid database name, SSH host alias, or remote backup directory.'
    exit 1
fi

for config_file in "$mysql_config" "$ssh_config"; do
    if [[ ! -r $config_file ]]; then
        log "Cannot read configuration file $config_file"
        exit 1
    fi
done

mkdir -p -- "$backup_dir"
exec 9>"$backup_dir/.backup.lock"
if ! flock -n 9; then
    log 'Another backup is already running.'
    exit 1
fi

if command -v mariadb-dump >/dev/null 2>&1; then
    dump=mariadb-dump
else
    dump=mysqldump
fi

timestamp=$(date -u +%Y-%m-%d_%H-%M-%S)
name="${database}_${timestamp}.sql.gz"
backup_file="$backup_dir/$name"
partial=$(mktemp -- "$backup_dir/.${name}.XXXXXXXX")
trap 'rm -f -- "$partial"' EXIT
trap 'exit 1' HUP INT TERM

log "Creating backup $name"
if ! "$dump" --defaults-extra-file="$mysql_config" --single-transaction --quick \
    --no-tablespaces --hex-blob --routines --events --triggers --databases "$database" | gzip -c > "$partial"; then
    log 'Database dump or compression failed.'
    exit 1
fi
gzip -t -- "$partial"
mv -- "$partial" "$backup_file"
checksum=$(sha256sum < "$backup_file")
checksum=${checksum%% *}

ssh_options=(-F "$ssh_config" -o BatchMode=yes -o StrictHostKeyChecking=yes
    -o ConnectTimeout=15 -o ServerAliveInterval=15 -o ServerAliveCountMax=3)

for attempt in 1 2 3; do
    if scp "${ssh_options[@]}" -- "$backup_file" "$remote_host:$remote_dir/.$name.part" &&
        ssh "${ssh_options[@]}" -- "$remote_host" \
            "cd '$remote_dir' && printf '%s  %s\n' '$checksum' '.$name.part' | sha256sum -c - && mv -- '.$name.part' '$name'"; then
        log "Verified remote backup $name"
        break
    fi

    if (( attempt == 3 )); then
        log "Transfer failed after $attempt attempts. Local backup kept at $backup_file"
        exit 1
    fi
    log "Transfer attempt $attempt failed. Retrying."
    sleep "$((attempt * 15))"
done

# The UTC timestamp in each filename sorts chronologically.
export LC_ALL=C
shopt -s nullglob
backups=("$backup_dir/${database}_"????-??-??_??-??-??.sql.gz)
if (( ${#backups[@]} > 3 )); then
    rm -- "${backups[@]:0:${#backups[@]}-3}"
fi

if ! ssh "${ssh_options[@]}" -- "$remote_host" \
    "find '$remote_dir' -maxdepth 1 -type f -name '${database}_????-??-??_??-??-??.sql.gz' -mmin +4320 -delete"; then
    log 'Backup verified, but remote retention cleanup failed.'
    exit 1
fi

# Rumahweb / cPanel deployment

Laravel 12 requires PHP >= 8.2 and the standard PHP extensions documented by Laravel. Rumahweb's cPanel documentation states that cPanel hosting currently exposes PHP versions up to 8.4, so PHP 8.2+ should be selected in MultiPHP Manager/Select PHP Version before deployment.

## Recommended layout

```text
/home/USERNAME/berita-auto-laravel/
  app/
  bootstrap/
  config/
  database/
  resources/
  routes/
  storage/
  vendor/
  public/
```

Set the domain document root to:

```text
/home/USERNAME/berita-auto-laravel/public
```

Do not point the domain at the repository root. This prevents `.env`, `app/`, `config/`, and other private files from being web-accessible.

## cPanel setup

1. Create a MySQL database and user in cPanel.
2. Select PHP 8.2 or newer and enable `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, and `xml`.
3. Create the repository directory using cPanel Git Version Control or the deployment artifact.
4. Configure the domain document root to `public/`.
5. Upload the production `.env` outside Git.
6. Run migrations through the deployment pipeline or cPanel-supported command mechanism.
7. Ensure `storage/` and `bootstrap/cache/` are writable by the web process.
8. Configure one cron entry:

```text
*/5 * * * * /usr/local/bin/ea-php82 /home/USERNAME/berita-auto-laravel/artisan schedule:run >> /home/USERNAME/logs/berita-auto-scheduler.log 2>&1
```

Use the actual PHP CLI binary/version shown by the account; Rumahweb documents version-specific `ea-php#` binaries for cPanel cron jobs.

## Production safety

Keep these values false until cutover approval:

```env
AUTOMATION_ENABLED=false
INSTAGRAM_PREPARATION_ENABLED=false
INSTAGRAM_AUTO_PUBLISH=false
```

Laravel must not be enabled as the Instagram publisher while the old publisher is still active.

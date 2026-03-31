# Mautic KB Pages Plugin

Knowledge base pages for Mautic with grouped article trees, host-aware root routing, and per-root public shell settings.

## Features

- Replaces tracking domains in emails based on sender email address
- Rewrites list unsubscribe, image pixel, webview and unsubscribe tokens
- Rewrites all tracking JS domains to match the HTTP request domain (CNAME-aware)
- REST API for managing domain mappings (`/api/kbpages`)
- Role-based permissions for domain management
- Owner-as-mailer support (uses contact owner's email for domain lookup)
- RFC-compliant Message-ID headers per domain

## What does it do and why you need it

https://www.youtube.com/watch?v=O8_pcHMXV-M

## Requirements

- Mautic 7.x (Docker FPM image)
- PHP 8.0+

## Installation

### Via Composer (Docker)

Ensure the composer and npm directories exist with correct permissions:

```bash
docker exec --user root mautic_web mkdir -p /var/www/.composer/cache
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.composer
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
```

If the Docker containers were recreated and these paths are not persisted, run the same commands again before `composer require` or `composer update`. Otherwise Composer may fail with errors like:

```text
Cannot create cache directory /var/www/.composer/cache/repo/...
```

For more stable updates, set these env vars when running Composer inside the container:

```bash
docker exec --user www-data \
  -e HOME=/var/www \
  -e COMPOSER_HOME=/var/www/.composer \
  -e COMPOSER_CACHE_DIR=/var/www/.composer/cache \
  --workdir /var/www/html mautic_web \
  composer update radata/mautic-kb-pages -W --no-interaction --ignore-platform-req=ext-gd
```

Allow dev packages (only needed once per Mautic installation):

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config minimum-stability dev
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config prefer-stable true
```

Add the GitHub repository and install the plugin:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config repositories.mautic-kb-pages vcs \
  https://github.com/radata/mautic-kb-pages --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer require radata/mautic-kb-pages:dev-main \
  -W --no-interaction --ignore-platform-req=ext-gd
```

# Check whether Mautic registered the plugin version:
```bash
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console doctrine:query:sql --force-fetch "SELECT id, bundle, name, version FROM plugins WHERE bundle='MauticKbPagesBundle'"
```

> The `--ignore-platform-req=ext-gd` flag is needed because the `mautic/mautic:7.0-fpm` Docker image has a broken GD CLI extension (`libavif.so.15` missing). GD works fine at runtime via PHP-FPM.

Update to the latest version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer update radata/mautic-kb-pages \
  -W --no-interaction --ignore-platform-req=ext-gd
```

Recommended update command with explicit Composer cache env:

```bash
docker exec --user www-data \
  -e HOME=/var/www \
  -e COMPOSER_HOME=/var/www/.composer \
  -e COMPOSER_CACHE_DIR=/var/www/.composer/cache \
  --workdir /var/www/html mautic_web \
  composer update radata/mautic-kb-pages -W --no-interaction --ignore-platform-req=ext-gd
docker exec --user www-data --workdir /var/www/html mautic_web \
  php bin/console mautic:plugins:reload
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod /var/www/html/var/cache/dev
docker exec --user www-data --workdir /var/www/html mautic_web \
  php bin/console cache:warmup --env=prod

```

```bash
docker exec mautic_web tail -5 /var/www/html/var/logs/mautic_prod-$(date +%Y-%m-%d).php
```

## if any columns are missing. 
```bash
docker exec --user www-data \
  -e HOME=/var/www \
  -e COMPOSER_HOME=/var/www/.composer \
  --workdir /var/www/html mautic_web \
  php bin/console doctrine:schema:update --force

docker exec --user www-data --workdir /var/www/html mautic_web \
  php bin/console doctrine:query:sql \
  "ALTER TABLE kb_pages ADD COLUMN theme VARCHAR(100) NULL DEFAULT NULL"


```

### Plugin Migrations

Container restart logs only show Mautic core migrations. Rebooting the Docker containers does **not** apply KB plugin schema changes by itself.

For this plugin, schema updates are picked up when all of these are true:

1. the new plugin code is installed with `composer require` or `composer update`
2. the plugin `version` in [Config/config.php](Config/config.php) changed
3. `php bin/console mautic:plugins:reload` is executed

Use this to confirm the installed plugin version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  php bin/console doctrine:query:sql --force-fetch "SELECT id, bundle, name, version FROM plugins WHERE bundle='MauticKbPagesBundle'"
```

Use this to verify the KB table schema after an update:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  php bin/console doctrine:query:sql --force-fetch "DESCRIBE kb_pages"
```

If a plugin migration was missed, first run `mautic:plugins:reload` again after the composer update. Only use manual `ALTER TABLE` fallback if the plugin version is correct and the schema is still missing expected columns.

If the npm post-install hook fails after composer require, fix it:

```bash
docker exec --user root mautic_web rm -rf /var/www/html/node_modules
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
docker exec --user www-data --workdir /var/www/html mautic_web npm ci --no-audit
```

### Post-Installation

Clear cache (hard delete required), reload plugins, then enable in UI:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod /var/www/html/var/cache/dev
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
```

1. Go to **Settings > Plugins > Multi Domain**
2. Set **Published** to **Yes**
3. Go to **Multi Domain** menu item > **New**: enter sender email + tracking domain
4. Ensure the tracking domain has a CNAME pointing to your Mautic URL

## API


REST API for managing domain mappings:

- **Get domain**: `GET /api/kbpages/ID`
- **List all**: `GET /api/kbpages`
- **Create**: `POST /api/kbpages/new` (body: `email`, `domain`)
- **Edit**: `PUT /api/kbpages/ID/edit` or `PATCH /api/kbpages/ID/edit` (body: `email`, `domain`)
- **Delete**: `DELETE /api/kbpages/ID/delete`

## Permissions

The plugin uses the Mautic permissions system. Roles can be configured for domain management access.

## Plugin Structure

```
plugins/MauticKbPagesBundle/
├── Assets/img/
│   └── icon.png                             # Plugin icon
├── Config/config.php                        # Service, route & menu registration
├── Controller/
│   ├── Api/
│   │   └── KbPagesApiController.php     # REST API controller
│   └── KbPagesController.php            # UI controller (list, create, edit, delete)
├── Entity/
│   ├── KbPages.php                      # Domain mapping entity (email + domain)
│   └── KbPagesRepository.php            # Database queries
├── Event/
│   └── KbPagesEvent.php                 # Custom event class
├── EventListener/
│   ├── BuilderSubscriber.php                # Rewrites tracking URLs in emails
│   ├── BuildJsSubscriber.php                # Rewrites tracking JS domains
│   └── MultidomianSubscriber.php            # Audit log & domain event handling
├── Form/Type/
│   └── KbPagesType.php                  # Domain mapping form
├── Model/
│   └── KbPagesModel.php                 # Business logic & domain lookups
├── Resources/views/KbPages/
│   ├── details.html.twig                    # Detail view
│   ├── form.html.twig                       # Create/edit form
│   ├── index.html.twig                      # Index page
│   └── list.html.twig                       # List table
├── Security/Permissions/
│   └── KbPagesPermissions.php           # Role-based access control
├── Translations/
│   └── en_US/
│       ├── messages.ini
│       └── validators.ini
├── MauticKbPagesBundle.php              # Bundle class
└── composer.json
```

## Uninstall

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer remove radata/mautic-kb-pages -W --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config --unset repositories.mautic-kb-pages
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
```

## Credits

Original work: https://github.com/friendly-ch/mautic-kb-pages
Upgrade by: https://github.com/rjocoleman

## License

MIT - see [LICENSE](LICENSE) for details.


<div class="row-fluid-wrapper row-depth-1 row-number-5 dnd-row" style="-webkit-text-stroke-width:0px;background-color:rgb(249, 252, 255);box-sizing:border-box;color:rgb(68, 68, 68);font-family:&quot;Pathway Extreme&quot;, sans-serif;font-size:16px;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">
    <div class="row-fluid " style="box-sizing:border-box;display:flex;flex-wrap:nowrap;justify-content:space-between;width:704.25px;">
        <div class="span12 widget-span widget-type-custom_widget dnd-module" style="box-sizing:border-box;min-height:1px;width:704.25px;" data-widget-type="custom_widget" data-x="0" data-w="12">
            <div class="hs_cos_wrapper hs_cos_wrapper_widget hs_cos_wrapper_type_module" style="box-sizing:border-box;" id="hs_cos_wrapper_kb-article-module-5" data-hs-cos-general-type="widget" data-hs-cos-type="module">
                <div class="hs_cos_wrapper hs_cos_wrapper_widget hs_cos_wrapper_type_inline_richtext_field" style="box-sizing:border-box;overflow-y:auto;" id="hs_cos_wrapper_kb-article-module-5_" data-hs-cos-general-type="widget" data-hs-cos-type="inline_richtext_field">
                    <p style="box-sizing:border-box;margin:0px 0px 1.4rem;">
                        <strong style="box-sizing:border-box;font-weight:bolder;">Heb jij een problemen met inloggen?</strong> Dit kan alleen via de app worden opgelost. Wanneer jouw wachtwoord niet werkt, kan je op het inlogscherm aangeven dat je het wachtwoord bent vergeten en een nieuwe wilt. Vul jouw email adres in waarmee je een account hebt aangemaakt, Hollandworx zal je vervolgens een email sturen om je wachtwoord te resetten.&nbsp;
                    </p>
                    <p style="box-sizing:border-box;margin:0px 0px 1.4rem;">
                        Herkent de app jouw email adres niet? Stuur dan een mailtje naar <a style="background-color:transparent;box-sizing:border-box;color:rgb(68, 68, 68);cursor:pointer;font-family:&quot;Pathway Extreme&quot;, sans-serif;font-style:normal;font-weight:400;line-height:1.4;text-decoration:rgb(68, 68, 68);text-transform:none;" href="mailto:support@hollandworx.nl" rel="noopener">support@hollandworx.nl</a>, zij zullen kijken of het probleem kan worden opgelost.
                    </p>
                    <div class="hs-callout-type-caution" style="-webkit-text-stroke-width:0px;background-color:rgb(253, 237, 238);border-left:5px solid rgb(248, 169, 173);box-sizing:border-box;clear:both;color:rgb(0, 0, 0);font-family:&quot;Pathway Extreme&quot;, sans-serif;font-size:16px;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;margin:0px 0px 1.4rem;orphans:2;padding:20px 30px;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;width:704.25px;word-spacing:0px;" data-hs-callout-type="caution">
                        <p style="box-sizing:border-box;margin:0px;">
                            Let op: Als je hoofdletters in je email adres hebt gebruikt dan moet het nu weer. Check je email van ons of neem contact met ons op.
                        </p>
                    </div>
                    <div class="hs-callout-type-caution" style="background-color:rgb(253, 237, 238);border-left:5px solid rgb(248, 169, 173);box-sizing:border-box;clear:both;color:rgb(0, 0, 0);margin:0px 0px 1.4rem;padding:20px 30px;width:704.25px;" data-hs-callout-type="caution">
                        <p style="box-sizing:border-box;margin:0px;">
                            Let op: geen mail ontvangen? Check altijd even je spambox. Het kan soms voorkomen dat de mail daar belandt.&nbsp;
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
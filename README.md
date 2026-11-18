# Agent QR Code for GLPI

GLPI 11 plugin that shows a QR code and a deeplink to configure the [GLPI Agent for Android](https://github.com/glpi-project/android-inventory-agent) in one scan. It's a free alternative to the GLPI Network "Android Agent configuration" plugin.

## What goes in the code

The app reads a base64-encoded JSON object (see `ActivityDetailServer.java` in the app). The plugin fills it with:

- `URL`: the inventory URL (default `<GLPI URL>/front/inventory.php`)
- `TAG`: an optional inventory tag
- `ASSET_ITEMTYPE`: `Phone` or `Computer`
- `ANDROID_AUTOMATIC_INVENTORY`, `ANDROID_FREQUENCY` (`Day`, `Week` or `Month`) and `ANDROID_START_ON_BOOT`
- `CLIENT_ID` and `CLIENT_SECRET`: from a GLPI 11 OAuth client using the `client_credentials` grant with the `inventory` scope, if you pick one

`LOGIN` and `PASSWORD` are always sent empty. Authentication uses OAuth.

The deeplink is `https://deeplink-agent.glpi-project.org/?data=<same base64>`. If it is opened on the phone, the app opens with the fields already filled in.

## Usage

1. In GLPI, create an OAuth client: Setup › OAuth clients, grant *Client credentials*, scope *inventory*.
2. Setup › Agent QR Code: choose that client, adjust the options and save.
3. In the app: Advanced settings › Server list › Add › QR Code, then scan.

## Security

The QR code contains the OAuth client secret. Anyone with a photo of it can send inventory to your GLPI. For that reason the page is only shown to profiles with the `config` UPDATE right. Don't print the QR code or share it. If the secret leaks, regenerate it on the OAuth client.

## Install

```sh
cp -r glpi-agentqrcode /var/www/html/glpi/plugins/agentqrcode
php bin/console plugin:install -u glpi agentqrcode
php bin/console plugin:activate agentqrcode
```

No database tables are created. Settings are stored in `glpi_configs` (context `plugin:agentqrcode`).

## Tests

```sh
php -d zend.assertions=1 -d assert.exception=1 tests/payload_test.php
```

## License

GPLv3+

<?php

/**
 * Builds the QR code / deeplink payload read by the GLPI Agent for Android
 * (org.glpi.inventory.agent.ui.ActivityDetailServer): base64 of a JSON object.
 * URL, TAG, LOGIN, PASSWORD and ASSET_ITEMTYPE must always be present, or the app rejects the code.
 */
final class PluginAgentqrcodePayload
{
    public const ITEMTYPES   = ['Phone', 'Computer'];
    public const FREQUENCIES = ['Day', 'Week', 'Month'];
    public const DEEPLINK    = 'https://deeplink-agent.glpi-project.org/?data=';

    public static function build(array $conf, string $client_id = '', string $client_secret = ''): array
    {
        $data = [
            'URL'                         => (string) ($conf['url'] ?? ''),
            'TAG'                         => (string) ($conf['tag'] ?? ''),
            'LOGIN'                       => '',
            'PASSWORD'                    => '',
            'ASSET_ITEMTYPE'              => in_array($conf['itemtype'] ?? '', self::ITEMTYPES, true) ? $conf['itemtype'] : 'Phone',
            'ANDROID_AUTOMATIC_INVENTORY' => empty($conf['auto']) ? '0' : '1',
            'ANDROID_FREQUENCY'           => in_array($conf['frequency'] ?? '', self::FREQUENCIES, true) ? $conf['frequency'] : 'Day',
            'ANDROID_START_ON_BOOT'       => empty($conf['boot']) ? '0' : '1',
        ];
        if ($client_id !== '') {
            $data['CLIENT_ID']     = $client_id;
            $data['CLIENT_SECRET'] = $client_secret;
        }
        return $data;
    }

    public static function encode(array $data): string
    {
        return base64_encode(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    public static function deeplink(string $encoded): string
    {
        return self::DEEPLINK . rawurlencode($encoded);
    }
}

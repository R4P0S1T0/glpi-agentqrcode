<?php

define('PLUGIN_AGENTQRCODE_VERSION', '1.0.0');

require_once __DIR__ . '/src/Payload.php';

function plugin_version_agentqrcode()
{
    return [
        'name'         => 'Agent QR Code',
        'version'      => PLUGIN_AGENTQRCODE_VERSION,
        'author'       => 'Renan Morano',
        'license'      => 'GPLv3+',
        'homepage'     => 'https://github.com/R4P0S1T0/glpi-agentqrcode',
        'requirements' => ['glpi' => ['min' => '11.0', 'max' => '11.1']],
    ];
}

function plugin_agentqrcode_check_prerequisites()
{
    return true;
}

function plugin_agentqrcode_check_config()
{
    return true;
}

function plugin_init_agentqrcode()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['agentqrcode'] = true;
    // The page shows the OAuth client secret, so only profiles that can change the GLPI setup see it.
    if (Session::haveRight('config', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['agentqrcode'] = 'front/config.php';
    }
}

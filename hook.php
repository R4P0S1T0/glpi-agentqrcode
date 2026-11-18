<?php

// No tables: settings live in glpi_configs (context "plugin:agentqrcode").
function plugin_agentqrcode_install()
{
    return true;
}

function plugin_agentqrcode_uninstall()
{
    Config::deleteConfigurationValues('plugin:agentqrcode', ['url', 'tag', 'itemtype', 'auto', 'frequency', 'boot', 'oauthclients_id']);
    return true;
}

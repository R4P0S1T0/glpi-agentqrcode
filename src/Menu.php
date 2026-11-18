<?php

/** Entry under Setup, shown only to profiles that may see the OAuth secret. */
final class PluginAgentqrcodeMenu extends CommonGLPI
{
    public static function getTypeName($nb = 0)
    {
        return 'Agent QR Code';
    }

    public static function getIcon()
    {
        return 'ti ti-qrcode';
    }

    public static function getMenuContent()
    {
        global $CFG_GLPI;
        if (!Session::haveRight('config', UPDATE)) {
            return false;
        }
        $page = $CFG_GLPI['root_doc'] . '/plugins/agentqrcode/front/config.php';
        return [
            'title' => self::getTypeName(),
            'icon'  => self::getIcon(),
            'page'  => $page,
            'links' => ['search' => $page],
        ];
    }
}

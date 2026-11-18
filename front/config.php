<?php

include('../../../inc/includes.php');

use Com\Tecnick\Barcode\Barcode;

Session::checkRight('config', UPDATE);

global $CFG_GLPI;
$context = 'plugin:agentqrcode';
$self    = $CFG_GLPI['root_doc'] . '/plugins/agentqrcode/front/config.php';

if (isset($_POST['save'])) {
    Config::setConfigurationValues($context, [
        'url'             => trim((string) ($_POST['url'] ?? '')),
        'tag'             => trim((string) ($_POST['tag'] ?? '')),
        'itemtype'        => (string) ($_POST['itemtype'] ?? 'Phone'),
        'auto'            => empty($_POST['auto']) ? 0 : 1,
        'frequency'       => (string) ($_POST['frequency'] ?? 'Day'),
        'boot'            => empty($_POST['boot']) ? 0 : 1,
        'oauthclients_id' => (int) ($_POST['oauthclients_id'] ?? 0),
    ]);
    Html::redirect($self);
}

$conf = Config::getConfigurationValues($context) + [
    'url' => $CFG_GLPI['url_base'] . '/front/inventory.php', 'tag' => '', 'itemtype' => 'Phone',
    'auto' => 1, 'frequency' => 'Day', 'boot' => 1, 'oauthclients_id' => 0,
];

$clients = getAllDataFromTable('glpi_oauthclients', ['is_active' => 1]);
$client_id = $client_secret = '';
if (isset($clients[$conf['oauthclients_id']])) {
    $client_id     = $clients[$conf['oauthclients_id']]['identifier'];
    $client_secret = (string) (new GLPIKey())->decrypt($clients[$conf['oauthclients_id']]['secret']);
}

$encoded  = PluginAgentqrcodePayload::encode(PluginAgentqrcodePayload::build($conf, $client_id, $client_secret));
$deeplink = PluginAgentqrcodePayload::deeplink($encoded);
$png      = (new Barcode())->getBarcodeObj('QRCODE,M', $encoded, -6, -6, 'black', [4, 4, 4, 4])
    ->setBackgroundColor('white')->getPngData();

$e = fn($v) => htmlescape((string) $v);
$option = fn($value, $label, $current) => '<option value="' . $e($value) . '"' . ((string) $value === (string) $current ? ' selected' : '') . '>' . $e($label) . '</option>';

Html::header('Agent QR Code', '', 'config', PluginAgentqrcodeMenu::class);
echo '<div class="container-fluid"><div class="row g-4">';

echo '<div class="col-lg-6"><form method="post" action="' . $e($self) . '" class="card"><div class="card-body">';
echo '<h3 class="card-title">' . $e(__('Configuração do GLPI Agent (Android)')) . '</h3>';
echo '<div class="mb-3"><label class="form-label">URL do servidor</label><input class="form-control" name="url" value="' . $e($conf['url']) . '"></div>';
echo '<div class="mb-3"><label class="form-label">Tag</label><input class="form-control" name="tag" value="' . $e($conf['tag']) . '"></div>';
echo '<div class="mb-3"><label class="form-label">Tipo do ativo</label><select class="form-select" name="itemtype">';
foreach (PluginAgentqrcodePayload::ITEMTYPES as $t) {
    echo $option($t, $t === 'Phone' ? 'Telefone' : 'Computador', $conf['itemtype']);
}
echo '</select></div>';
echo '<div class="mb-3"><label class="form-label">Client OAuth (client credentials)</label><select class="form-select" name="oauthclients_id">';
echo $option(0, '----- (sem OAuth)', $conf['oauthclients_id']);
foreach ($clients as $c) {
    echo $option($c['id'], $c['name'], $conf['oauthclients_id']);
}
echo '</select></div>';
echo '<div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="aqr-auto" name="auto" value="1"' . ($conf['auto'] ? ' checked' : '') . '><label class="form-check-label" for="aqr-auto">Inventário automático</label></div>';
echo '<div class="mb-3"><label class="form-label">Frequência</label><select class="form-select" name="frequency">';
foreach (['Day' => 'Diário', 'Week' => 'Semanal', 'Month' => 'Mensal'] as $v => $l) {
    echo $option($v, $l, $conf['frequency']);
}
echo '</select></div>';
echo '<div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="aqr-boot" name="boot" value="1"' . ($conf['boot'] ? ' checked' : '') . '><label class="form-check-label" for="aqr-boot">Iniciar com o aparelho</label></div>';
echo '<button class="btn btn-primary" type="submit" name="save" value="1">' . $e(_x('button', 'Save')) . '</button>';
echo '</div>';
Html::closeForm();
echo '</div>';

echo '<div class="col-lg-6"><div class="card"><div class="card-body text-center">';
echo '<h3 class="card-title">QR Code</h3>';
echo '<img alt="QR Code" style="max-width:100%;image-rendering:pixelated" src="data:image/png;base64,' . base64_encode($png) . '">';
echo '<p class="mt-3">No app: Configurações avançadas &rsaquo; Lista de servidores &rsaquo; Adicionar &rsaquo; QR Code.</p>';
echo '<p><a class="btn btn-outline-secondary" href="' . $e($deeplink) . '">Deeplink (abrir no celular)</a></p>';
if ($client_id !== '') {
    echo '<div class="alert alert-warning text-start">Este QR code contém o segredo do client OAuth. Quem tiver uma foto dele consegue enviar inventário. Não imprima nem compartilhe.</div>';
}
echo '</div></div></div>';

echo '</div></div>';
Html::footer();

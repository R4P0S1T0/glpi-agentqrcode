<?php

// php -d zend.assertions=1 -d assert.exception=1 tests/payload_test.php
require __DIR__ . '/../src/Payload.php';

$P = PluginAgentqrcodePayload::class;

// The app calls getString() on these, so they must exist even when empty.
$d = $P::build([]);
foreach (['URL', 'TAG', 'LOGIN', 'PASSWORD', 'ASSET_ITEMTYPE'] as $k) {
    assert(array_key_exists($k, $d));
}
assert($d['ASSET_ITEMTYPE'] === 'Phone' && !isset($d['CLIENT_ID']));
assert($P::build(['itemtype' => 'Monitor', 'frequency' => 'Year'])['ASSET_ITEMTYPE'] === 'Phone');

$d = $P::build(['url' => 'https://x/front/inventory.php', 'auto' => 1, 'frequency' => 'Week'], 'id', 's+/=');
assert($d['CLIENT_ID'] === 'id' && $d['CLIENT_SECRET'] === 's+/=' && $d['ANDROID_FREQUENCY'] === 'Week' && $d['ANDROID_AUTOMATIC_INVENTORY'] === '1');

// Round trip as the app does it: base64 -> JSON, and the deeplink query decodes back to the same base64.
$enc = $P::encode($d);
assert(json_decode(base64_decode($enc), true) === $d);
parse_str(parse_url($P::deeplink($enc), PHP_URL_QUERY), $q);
assert($q['data'] === $enc);

echo "ok\n";

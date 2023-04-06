<?php
ini_set('memory_limit', -1);
$defaultCallback = function($item) {
    $code = $item['kode'] ?? $item['nr'] ?? $item['nummer'];
    $name = $item['navn'];
    return ['label' => "$name", 'value' => $code];
};
$resources = [
    'zipcodes'       => [
        'endpoint'      => '/postnumre',
        'callback' => function($item) {
            $code = $item['nr'];
            $name = $item['navn'];
            return ['label' => "$code $name", 'value' => $code];
        },
    ],
    'municipalities' => [
        'endpoint'      => '/kommuner',
        'callback' => $defaultCallback,
    ],
    'regions'        => [
        'endpoint'      => '/regioner',
        'callback' => $defaultCallback,
    ],
    'police' => [
        'endpoint' => '/politikredse',
        'callback' => $defaultCallback,
    ],
    'courts' => [
        'endpoint' => '/retskredse',
        'callback' => $defaultCallback,
    ],
    'churches' => [
        'endpoint' => '/sogne',
        'callback' => $defaultCallback
    ],
    'elections' => [
        'endpoint' => '/storkredse',
        'callback' => $defaultCallback
    ]
];
$allOptions = [];
foreach ($resources as $resourceName => $info) {
    $data = json_decode(file_get_contents(sprintf("https://api.dataforsyningen.dk%s?struktur=mini", $info['endpoint'])), true);
    $options = array_map($info['callback'], $data);
    usort($options, fn($a, $b) => $a['label'] <=> $b['label']);
    $allOptions[$resourceName] = $options;
}
$optionsJson = json_encode($allOptions);
$js = <<<JS
import axios from 'axios';

const cache = {};

export const fetchGeoJson = async (resource, id) => {
    const url = `https://api.dataforsyningen.dk/\${resource}/\${id}`;
    if (!cache[url]) {
        const params = {format: 'geojson'};
        if (resource === 'postnumre') {
            params.landpostnumre = 'true';
        }
        const response = await axios.get(url, { params });
        cache[url] = response.data
    }
    return cache[url];
}

export const options = $optionsJson;
JS;
$path = __DIR__ . '/src/dawa.js';
if (!file_exists($path) || file_get_contents($path) !== $js) {
    file_put_contents($path, $js);
    echo "Saved\n";
} else {
    echo "No changes\n";
}

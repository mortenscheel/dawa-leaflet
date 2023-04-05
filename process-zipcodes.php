<?php
ini_set('memory_limit', -1);
$completeGeoJson = file_get_contents('https://api.dataforsyningen.dk/postnumre?format=geojson&landpostumre=1');
$completeDecoded = json_decode($completeGeoJson, true);
$destinationFolder = __DIR__ . '/../public/zipcodes';
$zips = [];
foreach ($completeDecoded['features'] as $feature) {
    $code = $feature['properties']['nr'];
    $name = $feature['properties']['navn'];
    $path = $destinationFolder . "/$code.geojson";
    if (file_exists($path)) {
        unlink($path);
    }
    file_put_contents($path, json_encode($feature));
    $zips[] = ['value' => $code, 'label' => "$code $name"];
}
$zipsEncoded = json_encode($zips, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
file_put_contents(__DIR__ . '/../src/zipcodes.js', "export const zips = $zipsEncoded;");
echo 'ok';
<?php

$json = file_get_contents('https://cbase.codefor.nl/cbases');
$data = json_decode($json, true);

$fields = [
  'cbase' => [
    'id', // FIXME: create some unique universal identifier too
    'name',
    'slug',
    'admin_name',
    'admin_email',
    'token_encrypted',
    'image',
    'description',
    'language',
    'promote',
    'logo_image',
    'highlight_color',
    'disabled',
  ],
  'usecase' => [
    'id', // FIXME: create some unique universal identifier too
    'cbase_id',
    'name',
    'slug',
    'teaser',
    'description',
    'image',
    'type',
    'location',
    'country',
    'category',
    'organisation',
    'website',
    'download',
    'license',
    'contact_name',
    'contact_image',
    'contact_email',
  ],
];

foreach ($data['_embedded']['cbase'] as $cbase) {
    // sql insert usecase
    var_dump($cbase);
    foreach ($cbase['_embedded']['usecase'] as $usecase) {
        // sql insert usecase
        var_dump($usecase);
        exit();
    }
}

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
    $sql = "INSERT INTO cbase SET\n";
    foreach ($fields['cbase'] as $field) {
      $sql .= "{$field} = :{$field} /* {$cbase[$field]} */\n";
    }
    var_dump($sql);
    // pdo
    foreach ($cbase['_embedded']['usecase'] as $usecase) {
        // sql insert usecase
        $sql = "INSERT INTO usecase SET\n";
        foreach ($fields['usecase'] as $field) {
          $sql .= "{$field} = :{$field} /* {$usecase[$field]} */\n";
        }
        var_dump($sql);
        exit();
    }
}

<?php

$json = file_get_contents('https://cbase.codefor.nl/cbases');
$data = json_decode($json, true);

$conf = [
  'db' => [
    'name' => getenv('DB_NAME'),
    'host' => getenv('DB_HOST'),
    'port' => '3306',
    'user' => getenv('DB_USER'),
    'pass' => getenv('DB_PASS') // ENV
  ]
];

$dsn = "mysql:dbname={$conf['db']['name']};host={$conf['db']['host']};port={$conf['db']['port']};charset=utf8";
$pdo = new PDO($dsn, $conf['db']['user'], $conf['db']['pass']);

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
    $sql = "INSERT INTO cbases SET\n";
    $values = [];
    foreach ($fields['cbase'] as $field) {
      $sql .= "{$field} = :{$field} /* {$cbase[$field]} */\n";
      $values[$field] = $cbase[$field];
    }
    //var_dump($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    foreach ($cbase['_embedded']['usecase'] as $usecase) {
        // sql insert usecase
        $sql = "INSERT INTO projects SET\n";
        $values = [];
        foreach ($fields['usecase'] as $field) {
          $sql .= "{$field} = :{$field} /* {$usecase[$field]} */\n";
          $values[$field] = $cbase[$field];
        }
        //var_dump($sql);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        exit();
    }
}

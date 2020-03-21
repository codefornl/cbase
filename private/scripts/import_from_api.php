<?php

$json = file_get_contents('https://cbase.codefor.nl/cbases');
$data = json_decode($json, true);

$conf = [
  'db' => [
    'name' => getenv('DB_NAME'),
    'host' => getenv('DB_HOST'),
    'user' => getenv('DB_USER'),
    'pass' => getenv('DB_PASS'),
  ]
];

$dsn = "mysql:dbname={$conf['db']['name']};host={$conf['db']['host']};port={$conf['db']['port']};charset=utf8";
$pdo = new PDO($dsn, $conf['db']['user'], $conf['db']['pass']);

$fields = [
  'cbase' => [
    //'id', // FIXME: create some unique universal identifier too
    'name' => '',
    'slug' => '',
    'admin_name' => '',
    'admin_email' => '',
    'token_encrypted' => '',
    'image' => '',
    'description' => '',
    'language' => 'nld',
    'promote' => 0,
    'logo_image' => '',
    'highlight_color' => '',
    'disabled' => 0,
  ],
  'usecase' => [
    //'id', // FIXME: create some unique universal identifier too
    //'cbase_id' => 0,
    'name' => '',
    'slug' => '',
    'teaser' => '',
    'description' => '',
    'image' => '',
    'type' => '',
    'location' => '',
    'country' => '',
    'category' => '',
    'organisation' => '',
    'website' => '',
    'download' => '',
    'license' => '',
    'contact_name' => '',
    'contact_image' => '',
    'contact_email' => '',
  ],
];

// FIXME: NOT NEEDED AFTER WE INTRODUCE GUID
// TODO DELETE ALL DATA (
// DELETE FROM cbases
// DELETE FROM projects
// TODO RESET AUTO INCREMENT
// ALTER TABLE cbases AUTO_INCREMENT = 1
// ALTER TABLE projects AUTO_INCREMENT = 1

foreach ($data['_embedded']['cbase'] as $cbase) {
    $sql = "INSERT INTO cbases SET \n";
    $parts = [];
    $values = [];
    foreach ($fields['cbase'] as $field => $default) {
      $parts[] = "{$field}=:{$field}";
      $values[$field] = $cbase[$field] ?? $default;
    }
    $sql .= implode(",\n", $parts);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    $cbase_id = $pdo->lastInsertId();
    foreach ($cbase['_embedded']['usecase'] as $usecase) {
        $sql = "INSERT INTO projects SET \n";
        $parts = [
          "cbase_id=:cbase_id"
        ];
        $values = [
          "cbase_id" => $cbase_id
        ];
        foreach ($fields['usecase'] as $field => $default) {
          $parts[] = "{$field}=:{$field}";
          $values[$field] = $usecase[$field] ?? $default;
        }
        $sql .= implode(",\n", $parts);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
    }
}

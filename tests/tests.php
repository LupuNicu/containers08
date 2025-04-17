<?php

require_once __DIR__ . '/testframework.php';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$tests = new TestFramework();

//
// Tests pentru clasa Database
//

function testDbConnection() {
    global $config;
    $db = new Database($config['db_path']);
    return $db instanceof Database;
}

function testDbCount() {
    global $config;
    $db = new Database($config['db_path']);
    $db->Execute("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, name TEXT)");
    $db->Execute("DELETE FROM test"); // curățăm
    return $db->Count("test") === 0;
}

function testDbCreate() {
    global $config;
    $db = new Database($config['db_path']);
    $id = $db->Create("test", ['name' => 'TestName']);
    return is_numeric($id) && $id > 0;
}

function testDbRead() {
    global $config;
    $db = new Database($config['db_path']);
    $id = $db->Create("test", ['name' => 'ReadTest']);
    $row = $db->Read("test", $id);
    return $row['name'] === 'ReadTest';
}

function testDbUpdate() {
    global $config;
    $db = new Database($config['db_path']);
    $id = $db->Create("test", ['name' => 'OldName']);
    $db->Update("test", $id, ['name' => 'NewName']);
    $row = $db->Read("test", $id);
    return $row['name'] === 'NewName';
}

function testDbDelete() {
    global $config;
    $db = new Database($config['db_path']);
    $id = $db->Create("test", ['name' => 'ToDelete']);
    $db->Delete("test", $id);
    $row = $db->Read("test", $id);
    return $row === false;
}

function testDbFetch() {
    global $config;
    $db = new Database($config['db_path']);
    $db->Execute("DELETE FROM test");
    $db->Create("test", ['name' => 'FetchTest']);
    $results = $db->Fetch("SELECT * FROM test WHERE name = 'FetchTest'");
    return count($results) === 1 && $results[0]['name'] === 'FetchTest';
}

function testDbExecute() {
    global $config;
    $db = new Database($config['db_path']);
    $result = $db->Execute("DELETE FROM test");
    return is_int($result);
}

//
// Tests pentru clasa Page
//

function testPageRender() {
    $templatePath = __DIR__ . '/../index.tpl';
    file_put_contents($templatePath, "<h1>{{title}}</h1><p>{{content}}</p>");
    $page = new Page($templatePath);

    ob_start();
    $page->Render([
        'title' => 'TestTitle',
        'content' => 'TestContent'
    ]);
    $output = ob_get_clean();

    return strpos($output, 'TestTitle') !== false && strpos($output, 'TestContent') !== false;
}

//
// Adăugăm testele în TestFramework
//

$tests->add('Database connection', 'testDbConnection');
$tests->add('Database count', 'testDbCount');
$tests->add('Database create', 'testDbCreate');
$tests->add('Database read', 'testDbRead');
$tests->add('Database update', 'testDbUpdate');
$tests->add('Database delete', 'testDbDelete');
$tests->add('Database fetch', 'testDbFetch');
$tests->add('Database execute', 'testDbExecute');
$tests->add('Page render', 'testPageRender');

//
// Rulăm testele
//

$tests->run();
echo $tests->getResult();

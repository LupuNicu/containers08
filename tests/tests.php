<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';
require_once __DIR__ . '/testframework.php';

$tests = new TestFramework();

// test 1: check database connection
function testDbConnection() {
    global $config;
    $db = new Database($config['db']['path']);
    return assertExpression($db !== null, 'Database connected', 'Database connection failed');
}

// test 2: test count method
function testDbCount() {
    global $config;
    $db = new Database($config['db']['path']);
    $count = $db->Count('page');
    return assertExpression(is_numeric($count) && $count >= 0, 'Count returned valid number', 'Count failed');
}

// test 3: test create method
function testDbCreate() {
    global $config;
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'Test title', 'content' => 'Test content']);
    return assertExpression($id > 0, 'Create succeeded', 'Create failed');
}

// test 4: test read method
function testDbRead() {
    global $config;
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'Read Test', 'content' => 'Reading']);
    $record = $db->Read('page', $id);
    return assertExpression($record['title'] === 'Read Test', 'Read succeeded', 'Read failed');
}

// test 5: test update method
function testDbUpdate() {
    global $config;
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'Old Title', 'content' => 'Old content']);
    $db->Update('page', $id, ['title' => 'New Title', 'content' => 'New content']);
    $record = $db->Read('page', $id);
    return assertExpression($record['title'] === 'New Title', 'Update succeeded', 'Update failed');
}

// test 6: test delete method
function testDbDelete() {
    global $config;
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'To Delete', 'content' => 'To Delete']);
    $db->Delete('page', $id);
    $record = $db->Read('page', $id);
    return assertExpression(empty($record), 'Delete succeeded', 'Delete failed');
}

// test 7: test Page class render
function testPageRender() {
    $template = __DIR__ . '/../templates/index.tpl';
    file_put_contents($template, "<h1>{{title}}</h1><p>{{content}}</p>");
    $page = new Page($template);
    $output = $page->Render(['title' => 'Hello', 'content' => 'World']);
    return assertExpression(strpos($output, 'Hello') !== false && strpos($output, 'World') !== false, 'Page render succeeded', 'Page render failed');
}

// Adaugă testele
$tests->add('Database connection', 'testDbConnection');
$tests->add('Table count', 'testDbCount');
$tests->add('Data create', 'testDbCreate');
$tests->add('Data read', 'testDbRead');
$tests->add('Data update', 'testDbUpdate');
$tests->add('Data delete', 'testDbDelete');
$tests->add('Page render', 'testPageRender');

// Rulează testele
$tests->run();
echo $tests->getResult();

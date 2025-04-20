<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../site/config.php';
require_once __DIR__ . '/../site/modules/database.php';
require_once __DIR__ . '/../site/modules/page.php';

$tests = new TestFramework();

function testDbConnection() {
    global $config;
    try {
        $db = new Database($config["db"]["path"]);
        return assertExpression(true, "Connected to DB");
    } catch (Exception $e) {
        return assertExpression(false, "Connection failed");
    }
}

function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count > 0, "Found records", "No records found");
}

function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $id = $db->Create("page", ["title" => "Test", "content" => "Test content"]);
    return assertExpression($id > 0, "Created record", "Failed to create");
}

function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $record = $db->Read("page", 1);
    return assertExpression($record && isset($record["title"]), "Read record", "Failed to read");
}

function testPageRender() {
    $page = new Page(__DIR__ . '/../site/templates/index.tpl');
    $html = $page->Render(["title" => "Hello", "content" => "World"]);
    return assertExpression(strpos($html, "Hello") !== false, "Page rendered", "Render failed");
}

$tests->add("Database connection", "testDbConnection");
$tests->add("Database count", "testDbCount");
$tests->add("Database create", "testDbCreate");
$tests->add("Database read", "testDbRead");
$tests->add("Page render", "testPageRender");

$tests->run();
echo $tests->getResult();

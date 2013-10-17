<?php

require_once 'library/FacebookClient.php';
require_once 'library/FacebookGraph.php';

$config = require_once 'config/local.php';
$facebook = new FacebookClient($config['client']);
$facebook->setAccessToken($config['accessToken']);

$graph = new FacebookGraph($config['facebookId'], $facebook);

if (array_key_exists('type', $_GET)) {
    $type = (string) $_GET['type'];
} else {
    $type = '';
}

if (array_key_exists('count', $_GET)) {
    $count = (int) $_GET['count'];
} else {
    $count = 10;
}

if (array_key_exists('name', $_GET)) {
    $name = $_GET['name'];
} else {
    $name = '';
}

if (array_key_exists('id', $_GET)) {
    $id = $_GET['id'];
} else {
    $id = null;
}

if (array_key_exists('cache', $_GET)) {
    $graph->cache = (bool) $_GET['cache'];
}

switch ($type) {
    case 'feed':
        $json = $graph->getFeed($count, $name);
        break;
    case 'posts':
        $json = $graph->getPosts($count, $name);
        break;
    case 'stream':
        $json = $graph->getStream($count, $id);
        break;
    case 'user':
    default:
        $json = $graph->getUser();
        break;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
echo $json;

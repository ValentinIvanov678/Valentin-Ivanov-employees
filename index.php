<?php
include 'config.php';
include __DIR__.'/src/router.php';

$router = New Router($config);
$router->handleRequest();
?>

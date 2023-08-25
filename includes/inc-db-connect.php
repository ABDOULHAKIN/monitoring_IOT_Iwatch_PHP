<?php

$pdo = new PDO('mysql:host=localhost;dbname=module', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
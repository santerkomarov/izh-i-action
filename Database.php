<?php

/**
 * Подключение к БД.
 * При первом обращении создаёт один общий объект PDO
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = require __DIR__ . '/config.php'; 
        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4";

        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }    
        return self::$instance->pdo;
    }
}

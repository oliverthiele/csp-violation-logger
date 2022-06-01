<?php

namespace OliverThiele\CspViolationLogger\Environment;

use Dotenv\Dotenv;

class Init
{

    public static function loadSettings()
    {
        $dir = dirname(__DIR__, 2) . '/Private/';
        $dotenv = Dotenv::createImmutable($dir);

        try {
            $dotenv->load();
            $dotenv->required(['DB_HOST', 'DB_DB', 'DB_USER', 'DB_PASS']);
            $dotenv->required('DB_HOST')->notEmpty();
            $dotenv->required('DB_DB')->notEmpty();
            $dotenv->required('DB_USER')->notEmpty();
            $dotenv->required('DB_PASS')->notEmpty();
        } catch (\Throwable $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}

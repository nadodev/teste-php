<?php

namespace Infrastructure\Config;

class Config
{
    private static array $config = [
        // Default SMTP configuration for development
        'SMTP_HOST' => 'localhost',
        'SMTP_PORT' => '1025', // Default port for local SMTP testing
        'SMTP_USER' => '',
        'SMTP_PASS' => '',
        'SMTP_FROM' => 'system@localhost',
        'SMTP_FROM_NAME' => 'ERP System'
    ];

    public static function load(): void
    {
        // Load from .env file if it exists
        if (file_exists(__DIR__ . '/../../../.env')) {
            $envFile = file_get_contents(__DIR__ . '/../../../.env');
            $lines = explode("\n", $envFile);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                
                list($key, $value) = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        }

        // Set environment variables
        foreach (self::$config as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }
} 
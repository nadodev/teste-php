<?php

namespace Infrastructure\Config;

class Config
{
    private static array $config = [];

    public static function load(): void
    {
        $envFile = __DIR__ . '/../../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    self::$config[trim($key)] = trim($value);
                }
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    public static function getMailConfig(): array
    {
        return [
            'host' => self::get('SMTP_HOST', 'smtp.gmail.com'),
            'port' => (int) self::get('SMTP_PORT', 587),
            'username' => self::get('SMTP_USERNAME', ''),
            'password' => self::get('SMTP_PASSWORD', ''),
            'from_address' => self::get('MAIL_FROM_ADDRESS', ''),
            'from_name' => self::get('MAIL_FROM_NAME', 'ERP Store'),
            'encryption' => self::get('SMTP_ENCRYPTION', 'tls')
        ];
    }
} 
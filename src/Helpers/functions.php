<?php

if (!function_exists('dd')) {
    /**
     * Dump and die - função para debug
     * 
     * @param mixed ...$vars Variáveis para debug
     * @return void
     */
    function dd(...$vars): void
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo '<div style="background: #1e1e1e; color: #fff; padding: 20px; margin: 10px; border-radius: 5px; font-family: monospace;">';
            echo '<div style="color: #4ec9b0; margin-bottom: 10px;">Debug:</div>';
            
            foreach ($vars as $var) {
                echo '<pre style="margin: 0; padding: 10px; background: #2d2d2d; border-radius: 3px; overflow: auto;">';
                print_r($var);
                echo '</pre>';
            }
            
            echo '</div>';
        }
        die();
    }
}

if (!function_exists('d')) {
    /**
     * Dump - função para debug sem parar a execução
     * 
     * @param mixed ...$vars Variáveis para debug
     * @return void
     */
    function d(...$vars): void
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo '<div style="background: #1e1e1e; color: #fff; padding: 20px; margin: 10px; border-radius: 5px; font-family: monospace;">';
            echo '<div style="color: #4ec9b0; margin-bottom: 10px;">Debug:</div>';
            
            foreach ($vars as $var) {
                echo '<pre style="margin: 0; padding: 10px; background: #2d2d2d; border-radius: 3px; overflow: auto;">';
                print_r($var);
                echo '</pre>';
            }
            
            echo '</div>';
        }
    }
}

if (!function_exists('debug')) {
    /**
     * Função para debug que salva no log
     * 
     * @param string $message Mensagem de debug
     * @param mixed $data Dados para debug
     * @return void
     */
    function debug($message, $data = null): void
    {
        $log = date('Y-m-d H:i:s') . " - " . $message;
        if ($data !== null) {
            $log .= "\n" . print_r($data, true);
        }
        error_log($log);
    }
} 
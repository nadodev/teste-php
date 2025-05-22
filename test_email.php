<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Infrastructure/Config/Env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Infrastructure\Config\Env;

// Carrega as variáveis de ambiente
Env::load();

echo "=== TESTE DE ENVIO DE EMAIL ===\n";
echo "Host: " . Env::get('EMAIL_HOST') . "\n";
echo "Port: " . Env::get('EMAIL_PORT') . "\n";
echo "Username: " . Env::get('EMAIL_USERNAME') . "\n";
echo "From: " . Env::get('EMAIL_FROM') . "\n";
echo "===========================\n\n";

$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
$mail->Debugoutput = function($str, $level) {
    echo "PHPMailer Debug [$level]: $str\n";
};

try {
    $mail->isSMTP();
    $mail->Host = Env::get('EMAIL_HOST');
    $mail->SMTPAuth = true;
    $mail->Username = Env::get('EMAIL_USERNAME');
    $mail->Password = Env::get('EMAIL_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = Env::get('EMAIL_PORT');
    
    // Timeout reduzido
    $mail->Timeout = 30;
    
    // Desabilita verificação SSL para testes
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Configuração do email
    $mail->setFrom(Env::get('EMAIL_FROM'), Env::get('EMAIL_NAME'));
    $mail->addAddress('leonardo.geja@unoesc.edu.br'); // Email de teste
    $mail->addReplyTo(Env::get('EMAIL_FROM'));

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Teste de Email - ERP Store';
    $mail->Body = '
        <h1>Teste de Email</h1>
        <p>Este é um email de teste do sistema ERP Store.</p>
        <p>Se você está recebendo este email, significa que a configuração de email está funcionando corretamente.</p>
    ';
    $mail->AltBody = 'Teste de Email - ERP Store';

    echo "Tentando enviar email...\n";
    $mail->send();
    echo "Email enviado com sucesso!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 
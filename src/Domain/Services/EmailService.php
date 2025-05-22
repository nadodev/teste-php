<?php

namespace Domain\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Infrastructure\Config\Env;

class EmailService
{
    private string $host;
    private string $port;
    private string $username;
    private string $password;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->host = Env::get('EMAIL_HOST');
        $this->port = Env::get('EMAIL_PORT');
        $this->username = Env::get('EMAIL_USERNAME');
        $this->password = Env::get('EMAIL_PASSWORD');
        $this->fromEmail = Env::get('EMAIL_FROM');
        $this->fromName = Env::get('EMAIL_NAME');

    }

    public function enviarDetalhesCompra(string $email, array $itens, float $subtotal, float $desconto, float $frete, float $total): void
    {
        try {
            error_log("=== INICIANDO ENVIO DE EMAIL ===");
            error_log("Destinatário: " . $email);
            
            $html = $this->gerarHtmlPedido($itens, $subtotal, $desconto, $frete, $total);
            $text = $this->gerarTextoPlainPedido($itens, $subtotal, $desconto, $frete, $total);
            
            error_log("Conteúdo do email gerado com sucesso");
            
            $mail = new PHPMailer(true);

            // Configuração de debug mais detalhada
            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer Debug [$level]: $str");
            };

            // Configurações do servidor
            error_log("Configurando servidor SMTP...");
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->port;
            $mail->CharSet = 'UTF-8';
            
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            error_log("Configurando remetente e destinatário...");
            
            // Remetente e destinatário
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($email);
            $mail->addReplyTo($this->fromEmail);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Detalhes do pedido - ERP Store';
            $mail->Body = $html;
            $mail->AltBody = $text;

            error_log("Tentando enviar email...");
            
            // Tenta enviar o email com timeout
            set_time_limit(30);
            $mail->send();
            

        } catch (Exception $e) {
            error_log("Mensagem de erro: " . $e->getMessage());
            error_log("Código do erro: " . $e->getCode());
            error_log("Arquivo: " . $e->getFile());
            error_log("Linha: " . $e->getLine());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("============================");
            
            throw new \RuntimeException("Não foi possível enviar o email de confirmação. Por favor, tente novamente mais tarde.");
        }
    }

    private function gerarHtmlPedido(array $itens, float $subtotal, float $desconto, float $frete, float $total): string
    {
        $html = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h1 style="color: #333; text-align: center;">Detalhes do seu pedido</h1>
            <p style="color: #666;">Obrigado por comprar conosco! Abaixo estão os detalhes do seu pedido:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;">Produto</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid #dee2e6;">Qtd</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #dee2e6;">Preço</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid #dee2e6;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($itens as $item) {
            $html .= '
                <tr>
                    <td style="padding: 12px; border-bottom: 1px solid #dee2e6;">' . htmlspecialchars($item['produto']->getNome()) . '</td>
                    <td style="padding: 12px; text-align: center; border-bottom: 1px solid #dee2e6;">' . $item['quantidade'] . '</td>
                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">R$ ' . number_format($item['produto']->getPreco(), 2, ',', '.') . '</td>
                    <td style="padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;">R$ ' . number_format($item['subtotal'], 2, ',', '.') . '</td>
                </tr>';
        }

        $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold;">Subtotal:</td>
                        <td style="padding: 12px; text-align: right;">R$ ' . number_format($subtotal, 2, ',', '.') . '</td>
                    </tr>';

        if ($desconto > 0) {
            $html .= '
                    <tr>
                        <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold; color: #28a745;">Desconto:</td>
                        <td style="padding: 12px; text-align: right; color: #28a745;">- R$ ' . number_format($desconto, 2, ',', '.') . '</td>
                    </tr>';
        }

        $html .= '
                    <tr>
                        <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold;">Frete:</td>
                        <td style="padding: 12px; text-align: right;">R$ ' . number_format($frete, 2, ',', '.') . '</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold; font-size: 1.2em;">Total:</td>
                        <td style="padding: 12px; text-align: right; font-weight: bold; font-size: 1.2em;">R$ ' . number_format($total, 2, ',', '.') . '</td>
                    </tr>
                </tfoot>
            </table>
            
            <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
                <p style="margin: 0; color: #666;">
                    Se tiver alguma dúvida sobre seu pedido, entre em contato conosco respondendo este email.
                </p>
            </div>
        </div>';

        return $html;
    }

    private function gerarTextoPlainPedido(array $itens, float $subtotal, float $desconto, float $frete, float $total): string
    {
        $texto = "DETALHES DO SEU PEDIDO\n\n";
        $texto .= "Obrigado por comprar conosco! Abaixo estão os detalhes do seu pedido:\n\n";

        foreach ($itens as $item) {
            $texto .= sprintf(
                "%s\nQuantidade: %d\nPreço: R$ %s\nSubtotal: R$ %s\n\n",
                $item['produto']->getNome(),
                $item['quantidade'],
                number_format($item['produto']->getPreco(), 2, ',', '.'),
                number_format($item['subtotal'], 2, ',', '.')
            );
        }

        $texto .= "\nRESUMO DO PEDIDO\n";
        $texto .= sprintf("Subtotal: R$ %s\n", number_format($subtotal, 2, ',', '.'));
        
        if ($desconto > 0) {
            $texto .= sprintf("Desconto: - R$ %s\n", number_format($desconto, 2, ',', '.'));
        }
        
        $texto .= sprintf("Frete: R$ %s\n", number_format($frete, 2, ',', '.'));
        $texto .= sprintf("Total: R$ %s\n", number_format($total, 2, ',', '.'));

        return $texto;
    }
} 
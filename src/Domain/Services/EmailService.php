<?php

namespace Domain\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Infrastructure\Config\Config;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        
        // Carregar configurações do email
        $config = Config::getMailConfig();
        
        // Configuração do servidor SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'];
        $this->mailer->Password = $config['password'];
        $this->mailer->SMTPSecure = $config['encryption'];
        $this->mailer->Port = $config['port'];
        
        // Configuração do remetente
        $this->mailer->setFrom($config['from_address'], $config['from_name']);
        
        // Configuração do formato
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function enviarDetalhesCompra(string $email, array $itens, float $subtotal, float $desconto, float $frete, float $total): void
    {
        try {
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Detalhes do seu pedido - ERP Store';
            
            // Gera o HTML do email
            $html = $this->gerarHtmlPedido($itens, $subtotal, $desconto, $frete, $total);
            
            $this->mailer->Body = $html;
            $this->mailer->AltBody = $this->gerarTextoPlainPedido($itens, $subtotal, $desconto, $frete, $total);
            
            $this->mailer->send();
        } catch (Exception $e) {
            // Log do erro
            error_log("Erro ao enviar email: {$e->getMessage()}");
            throw new \RuntimeException('Não foi possível enviar o email. Por favor, tente novamente mais tarde.');
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
<?php

namespace Domain\Services;

use Exception;

class EmailService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;
    private string $testEmail;

    public function __construct()
    {
        $this->apiKey = 're_NLzSyNXc_GjhSp3zkgyqi1ks2wqzsGEuP';
        // Usando um domínio verificado do Resend temporariamente
        $this->fromEmail = 'onboarding@resend.dev';
        $this->fromName = 'ERP Store';
        $this->testEmail = 'nadojba@hotmail.com';
    }

    public function enviarDetalhesCompra(string $email, array $itens, float $subtotal, float $desconto, float $frete, float $total): void
    {
        try {
            // Em modo de teste, sempre envia para o email de teste
            $destinationEmail = $this->testEmail;
            error_log("Modo de teste: redirecionando email de {$email} para {$destinationEmail}");
            
            // Gera o conteúdo do email
            $html = $this->gerarHtmlPedido($itens, $subtotal, $desconto, $frete, $total);
            $text = $this->gerarTextoPlainPedido($itens, $subtotal, $desconto, $frete, $total);
            
            // Adiciona informação sobre o email original no corpo
            $html = "<div style='background-color: #f8f9fa; padding: 10px; margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 4px;'>
                        <strong>MODO DE TESTE</strong><br>
                        Email original seria enviado para: {$email}
                    </div>" . $html;
            
            error_log("Iniciando envio do email via Resend API...");
            
            // Prepara os dados para a API do Resend
            $data = [
                'from' => "{$this->fromName} <{$this->fromEmail}>",
                'to' => [$destinationEmail],
                'subject' => '[TESTE] Detalhes do pedido - ERP Store',
                'html' => $html,
                'text' => $text,
                'reply_to' => $this->testEmail
            ];

            // Configuração da requisição
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ]);

            // Faz a requisição
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // Log da resposta para debug
            error_log("Resposta do Resend: " . $response);
            error_log("HTTP Code: " . $httpCode);

            // Verifica a resposta
            if ($error) {
                throw new Exception("Erro CURL: " . $error);
            }

            if ($httpCode !== 200) {
                $responseData = json_decode($response, true);
                $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Erro desconhecido';
                throw new Exception("Erro na API do Resend (HTTP {$httpCode}): {$errorMessage}");
            }

            error_log("Email enviado com sucesso para: {$destinationEmail} (original: {$email})");

        } catch (Exception $e) {
            $erro = "Erro ao enviar email: {$e->getMessage()}";
            error_log($erro);
            throw new \RuntimeException($erro);
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
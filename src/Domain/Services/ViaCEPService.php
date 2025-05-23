<?php

namespace Domain\Services;

use GuzzleHttp\Client;


class ViaCEPService
{
    public function consultarCEP(string $cep): ?array
{
    $cep = preg_replace('/\D/', '', $cep);

    if (strlen($cep) !== 8) {
        return null;
    }

    $client = new Client([
        'base_uri' => 'https://viacep.com.br/ws/',
        'timeout'  => 5.0,
    ]);

    try {
        
        $response = $client->get("{$cep}/json/");
        $data = json_decode($response->getBody(), true);

        if (isset($data['erro']) || empty($data)) {
            return null;
        }

        return [
            'cep' => $data['cep'],
            'logradouro' => $data['logradouro'],
            'bairro' => $data['bairro'],
            'cidade' => $data['localidade'],
            'estado' => $data['uf']
        ];
    } catch (\Exception $e) {
        return null;
    }
}
} 
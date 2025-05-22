<?php

namespace Presentation;

use Domain\Services\CarrinhoService;

class View
{
    private string $viewPath;
    private array $data;
    private ?string $layout;
    private string $content;

    public function __construct()
    {
        $this->viewPath = __DIR__ . '/Views/';
        $this->layout = 'layout/main';
    }

    public function setLayout(?string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function render(string $view, array $data = []): void
    {
        $this->data = $data;
        
        // Extrai as variáveis para o escopo local
        extract($this->data);
        
        // Define o caminho do arquivo da view
        $viewFile = $this->viewPath . $view . '.php';
        
        // Verifica se o arquivo existe
        if (!file_exists($viewFile)) {
            throw new \Exception("View {$view} não encontrada");
        }
        
        // Inicia o buffer de saída
        ob_start();
        
        // Inclui o arquivo da view
        require $viewFile;
        
        // Obtém o conteúdo do buffer e limpa
        $this->content = ob_get_clean();
        
        // Se não houver layout definido, exibe apenas o conteúdo
        if ($this->layout === null) {
            echo $this->content;
            return;
        }
        
        // Define o caminho do arquivo do layout
        $layoutFile = $this->viewPath . $this->layout . '.php';
        
        // Verifica se o arquivo do layout existe
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout {$this->layout} não encontrado");
        }
        
        // Inclui o arquivo do layout
        require $layoutFile;
    }
} 
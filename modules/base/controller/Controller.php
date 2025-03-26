<?php
namespace base\controller;

class Controller {
    protected array $data = array();
    protected string $view = '';
    protected string $theme = 'default'; // Default theme
    protected array $header = array(
        'title' => 'MagnaShop2', 
        'keywords' => '', 
        'description' => ''
    );

    public function vypisView(): void {
        
        if ($this->view) {
            $this->data['pageTitle'] = $this->header['title'] ?? 'Magnashop';
            $this->data['metaTags'] = [
                'description' => $this->header['description'] ?? '',
                'keywords' => $this->header['keywords'] ?? '',
                'author' => $this->header['author'] ?? '',
            ];

            // Extract data to make variables available in view
            extract($this->data);

            $themePath = 'src/theme/' . $this->theme . '/layout/';
            $viewPath = 'modules/' . $this->view . '.php';
            
            // Include theme header
            require($themePath . 'header.php');
            
            // Include theme navbar
            require($themePath . 'navbar.php');
            
            // Include the specific view content
            require($viewPath);
            
            // Include theme footer
            require($themePath . 'footer.php');
        }
    }

    public function redirect(string $url): never {
        header('Location: ' . $url);
        header('Connection: close');
        exit();
    }

    // Method to set page title and meta information
    public function setHeader(array $headerData): void {
        $this->header = array_merge($this->header, $headerData);
    }
    
    // Method to change theme
    public function setTheme(string $theme): void {
        $this->theme = $theme;
    }

    // Vrací true, pokud jde o POST požadavek
    public function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    // Vrací true, pokud jde o GET požadavek
    public function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    // Získání hodnoty z GET/POST/REQUEST
    public function input(string $key, $default = null, string $method = 'REQUEST') {
        $source = match (strtoupper($method)) {
            'GET' => $_GET,
            'POST' => $_POST,
            'REQUEST' => $_REQUEST,
            default => []
        };

        return $source[$key] ?? $default;
    }
    // Nastavení proměnné do $this->data pro předání do view
    public function set(string $key, mixed $value): void {
        $this->data[$key] = $value;
    }
    
}

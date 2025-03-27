<?php
namespace base\controller;

use base\controller\Controller;

class RouterController extends Controller {
    
    protected Controller $controller;

    public function process(array $parameters): void {
        $parsedURL = $this->parseURL($parameters[0] ?? '');

        // Pokud je URL prázdná (domovská stránka)
        if (empty($parsedURL)) {
            $defaultController = new \base\controller\HomepageController();
            $defaultController->index();
            return;
        }

        // 1. část URL = modul (např. 'product', 'quote', 'login')
        $module = $parsedURL[0];

        // 2. část = akce (např. 'list', 'add', 'login', ...)
        $action = $parsedURL[1] ?? 'index';

        // 3. a další části = parametry
        $params = array_slice($parsedURL, 2);

        // Controller se jmenuje vždy <Modul>Controller (např. ProductController)
        $controllerName = ucfirst($module) . 'Controller';
        $controllerNamespace = "modules\\$module\\controller\\$controllerName";

        if (class_exists($controllerNamespace)) {
            $controllerObject = new $controllerNamespace();

            if (method_exists($controllerObject, $action)) {
                call_user_func_array([$controllerObject, $action], $params);
            } else {
                error_log("❌ Method nenalezena: $controllerNamespace::$action");
                self::show404();
            }
        } else {
            error_log("❌ Controller nenalezen: $controllerNamespace");
            self::show404();
        }
    }

    public static function show404() {
        require_once('modules/error/controller/ErrorController.php');
        $errorController = new \modules\error\controller\ErrorController();
        $errorController->notFound();
        exit();
    }

    private function parseURL(string $url): array {
        if (empty($url)) return [];

        $parsedURL = parse_url($url);
        $parsedURL['path'] = ltrim($parsedURL['path'] ?? '', '/');
        $parsedURL['path'] = trim($parsedURL['path']);

        $dividedPath = explode('/', $parsedURL['path']);
        return array_filter($dividedPath);
    }

    private function camelNotation(string $text): string {
        $sentence = str_replace('-', ' ', $text);
        $sentence = ucwords($sentence);
        $sentence = str_replace(' ', '', $sentence);
        return $sentence;
    }
}

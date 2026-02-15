<?php

namespace App\Core;

class Router
{
    protected $currentController = 'App\\Controllers\\HomeController'; // Default controller
    protected $currentMethod = 'index'; // Default method
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        // Check for controller
        if (isset($url[0])) {
            // Check if it's an admin controller (starts with 'admin')
            if ($url[0] === 'admin' && isset($url[1])) {
                // New format: admin/controller
                $controllerName = 'App\\Controllers\\Admin\\' . ucwords($url[1]) . 'Controller';
                if (class_exists($controllerName)) {
                    $this->currentController = $controllerName;
                    unset($url[0]);
                    unset($url[1]);
                    
                    // Shift method from index 2 to 1 if it exists so the method check logic works
                    if (isset($url[2])) {
                        $url[1] = $url[2];
                        unset($url[2]);
                    }
                }
            } elseif (stripos($url[0], 'admin') === 0) {
                // Legacy format: admincontroller
                // Extract controller name after 'admin' prefix
                $adminControllerName = substr($url[0], 5); // Remove 'admin' prefix
                $controllerName = 'App\\Controllers\\Admin\\' . ucwords($adminControllerName) . 'Controller';
                
                if (class_exists($controllerName)) {
                    $this->currentController = $controllerName;
                    unset($url[0]);
                }
            } else {
                // Regular controller
                $controllerName = 'App\\Controllers\\' . ucwords($url[0]) . 'Controller';
                if (class_exists($controllerName)) {
                    $this->currentController = $controllerName;
                    unset($url[0]);
                }
            }
        }

        $this->currentController = new $this->currentController;

        // Check for method
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}

<?php

namespace Framework;
use Framework\DI\Service;
class Application
{
    private $configs = [];

    public function __construct($conf){
        $this->configs = include $conf;
        Service::set('request',new \Framework\Request\Request());
       // Service::set('session',new Framework\Session\Session());
       // Service::set('security',new Framework\Security\Security());

       }

    public function devMod(){
        if($this->configs['mode'] === 'dev'){
            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        }else{
            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }
    }
    public function run(){
        $this->devMod();

        $route = new Router();
        $route->set($this->configs['routes']);

        $route = $route->getRoute();
        $controllerClass = $route['controller'];
        $actionClass = $route['action'].'Action';
        if (class_exists($controllerClass)) {
            $refl = new \ReflectionClass($controllerClass);
        } else {
            exit('No such controller');
        }
        if ($refl->hasMethod($actionClass)) {
            $controller     = $refl->newInstance();
            $action         = $refl->getMethod($actionClass);
            $action->invoke($controller);
        } else {
            exit('No such method');
        }
    }
}
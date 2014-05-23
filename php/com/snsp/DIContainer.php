<?php

namespace com\snsp;

/**
 * A basic Dependency Injection Container
 * //with some reflection / type hints we can extend it to act as an IoC
 * @author sanosay
 */
class DIContainer implements IDIContainer {

    private $services;
    private $serviceInstances;

    /**
     * Lifecycle type : Singleton 
     * Returns always the same instance
     * @var int 
     */
    public static $SINGLETON = 1;

    /**
     * Lifecycle type : Per call
     * Creates a new instsance on every request 
     * @var int 
     */
    public static $PER_CALL = 2;

    public function __construct() {
        $this->services = array();
        $this->serviceInstances = array();
    }

    /**
     * Registers a service / factory into the container
     * @param string $serviceName The service identifier 
     * @param object $func  The service or a closure        
     * @param int $lifecycle
     */
    public function register($serviceName, $func, $lifecycle = 1) {
        $this->services[$serviceName] = array($func, $lifecycle);
    }

    /**
     * Deregisters a service / factory from the container
     * @param string $serviceName
     */
    public function deregister($serviceName) {
        if ($this->isRegistered($serviceName)) {
            unset($this->services[$serviceName]);
            if (isset($this->serviceInstances[$serviceName])) {
                unset($this->serviceInstances[$serviceName]);
            }
        }
    }

    /**
     * Resolves a service / factory by name
     * @param string $serviceName The service name
     * @return callable The resolved service / factory
     */
    public function resolve($serviceName) {
        if ($this->isRegistered($serviceName)) {
            if ($this->services[$serviceName][1] == static::$SINGLETON) {
                if (isset($this->serviceInstances[$serviceName])) {
                    return $this->serviceInstances[$serviceName];
                } else {
                    $func = $this->services[$serviceName][0];
                  
                    if (is_object($func) && $func instanceof \Closure) {
                         $result = $this->services[$serviceName][0];
                        $this->serviceInstances[$serviceName] = $result($this);
                    } else {
                        $this->serviceInstances[$serviceName] = 
                                $this->services[$serviceName][0];
                       
                    }
                    return $this->serviceInstances[$serviceName];
                }
            } else {
                $func = $this->services[$serviceName][0];
                if ((is_object($func) && $func instanceof \Closure)) {
                    $result = $this->services[$serviceName][0];
                    return $result($this);
                    
                } else { 
                    return $this->services[$serviceName][0];
                }
            }
        }
        return null;
    }

    /**
     * Determine if a service has been registered
     * @param string $serviceName The service name
     */
    public function isRegistered($serviceName) {
        return isset($this->services[$serviceName]);
    }

    /**
     * Creates an instance of IDIContainer
     * @return \com\snsp\IDIContainer
     */
    public static function Create() {
        return new DIContainer();
    }

}

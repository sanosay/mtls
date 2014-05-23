<?php
namespace com\snsp;


/**
 *
 * @author sanosay
 */
interface IDIContainer {
   
    
     /**
     * Registers a service / factory into the container
     * @param string $serviceName The service identifier 
     * @param object $func  The service or a closure      
     * @param int $lifecycle
     */
    public function register($serviceName, $func, $lifecycle = 1);
    /**
     * Deregisters a service / factory from the container
     * @param string $serviceName
     */
    public function deregister($serviceName);
    /**
     * Resolves a service / factory by name
     * @param string $serviceName The service name
     * @return callable The resolved service / factory
     */
    public function resolve($serviceName);
     /**
     * Determine if a service has been registered
     * @param string $serviceName The service name
     */
    public function isRegistered($serviceName);
}

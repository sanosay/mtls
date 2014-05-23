<?php

namespace tests\com\snsp;

/**
 * Test of DIContainer
 * 
 * @author sanosay
 */
class DIContainerTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test register and isRegistered methods
     */
    public function testRegisterIsRegistered() {
        $container = \com\snsp\DIContainer::Create();
        $aClass = new \tests\com\snsp\mocks\AClass();
        $container->register('myAClass', $aClass);
        $this->assertTrue($container->isRegistered('myAClass'));

        $container->register('myFunc', function($di) {
            return 1;
        });
        $this->assertTrue($container->isRegistered('myFunc'));
        $this->assertFalse($container->isRegistered('myFunc2'));
    }

    /**
     * Test deregister and isRegistered methods
     */
    public function testDeregisterIsReigisterTest() {
        $container = \com\snsp\DIContainer::Create();
        $aClass = new \tests\com\snsp\mocks\AClass();
        $container->register('myAClass', $aClass);
        $this->assertTrue($container->isRegistered('myAClass'));

        $container->deregister('myAClass');
        $this->assertFalse($container->isRegistered('myAClass'));
    }

    /**
     * Test resolve method
     */
    public function testResolve() {
        $container = \com\snsp\DIContainer::Create();
        $aClass = new \tests\com\snsp\mocks\AClass();
        $container->register('myAClass', $aClass, 1);

        $resolvedAClass = $container->resolve('myAClass');
        $this->assertNotNull($resolvedAClass);
    }

    /**
     * Test resolve method with service lifecycle as singleton
     */
    public function testResolveSingleton() {
        $container = \com\snsp\DIContainer::Create();

        $container->register('myAClass', function($di) {
            return new \tests\com\snsp\mocks\AClass();
        }, \com\snsp\DIContainer::$SINGLETON);

        $resolvedAClass = $container->resolve('myAClass');
       
        $resolvedAClass->increaseCount();
        $this->assertEquals(1, $resolvedAClass->getCount());
        $resolvedSecondTimeAClass = $container->resolve('myAClass');
        $resolvedSecondTimeAClass->increaseCount();
        $this->assertEquals(2, $resolvedSecondTimeAClass->getCount());
        
    }
    /**
     * Test resolve method with  service lifecycle per call
     */
    public function testResolvePerCall() {
       
        $container = \com\snsp\DIContainer::Create();
      
        $container->register('myAClass',  function($di) {
            return new \tests\com\snsp\mocks\AClass();
        }, \com\snsp\DIContainer::$PER_CALL);
        $resolvedAClass = $container->resolve('myAClass');
       
        $resolvedAClass->increaseCount();
        $this->assertEquals(1, $resolvedAClass->getCount());
        $resolvedSecondTimeAClass = $container->resolve('myAClass');
        $resolvedSecondTimeAClass->increaseCount();
        $this->assertEquals(1, $resolvedSecondTimeAClass->getCount());
        
    }
    
    /**
     * Test resolved instance DI
     */
    public function testResolveDI() {
       
        $container = \com\snsp\DIContainer::Create();
      
        $container->register('myAClass',  function($di) {
            return new \tests\com\snsp\mocks\AClass();
        }, \com\snsp\DIContainer::$PER_CALL);
        $container->register('myBClass',  function($di) {
            return new \tests\com\snsp\mocks\BClass($di->resolve('myAClass'));
        }, \com\snsp\DIContainer::$PER_CALL);
        $resolvedBClass = $container->resolve('myBClass');
       
        $resolvedBClass->getAClass()->increaseCount();
        $this->assertEquals(1, $resolvedBClass->getAClass()->getCount());
        
        
    }

}

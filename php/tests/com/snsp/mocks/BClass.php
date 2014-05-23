<?php
namespace tests\com\snsp\mocks;

/**
 * A dummy class
 *
 * @author sanosay
 */
class BClass {
    private $count;
    private $aClass;
    /**
     * Constracts a BClass with given AClass
     * @param \tests\com\snsp\mocks\AClass $aClass 
     */
    public function __construct(AClass $aClass) {
        $this->count = 0;
        $this->aClass = $aClass;
    }
    /**
     * Gets the count
     * @return int 
     */
    public function getCount(){
        return $this->count;
    }
    /**
     * Increase a counter
     */
    public function increaseCount(){
        $this->count++;
    }
    /**
     * Gets the underling AClass asd 
     * @return \tests\com\snsp\mocks\AClass 
     */
    public function getAClass(){
        return $this->aClass;
    }
}

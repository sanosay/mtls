<?php
namespace tests\com\snsp\mocks;


/**
 * A dummy class
 *
 * @author sanosay
 */
class AClass {
    private $count;
    /**
     * Constructs an AClass
     */
    public function __construct() {
        $this->count = 0;
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
}

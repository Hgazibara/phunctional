<?php

namespace phunctional;


class FunctoolsTest extends \PHPUnit_Framework_TestCase {
    
    private function getAnyNumber() {
        return 10;
    }

    public function testPartial_createsNewFunction() {
        $this->assertTrue(\is_callable(
                func\partial(operators\operator('+'), $this->getAnyNumber())
        ));
    }
}

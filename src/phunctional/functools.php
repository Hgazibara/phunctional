<?php

namespace phunctional\func {

    /**
     * Creates a new function which is equal to an original function, except that
     * some number of original function's arguments are now 'freezed', e.g. some
     * arguments are pre-populated during every function call using parameters
     * received when this function was called.
     * 
     * @param callable $fn function whose arguments should be modified
     * @param mixed[] ...$arg variable number of arguments
     * 
     * @return callable new function
     * 
     * @throws \InvalidArgumentException if no parameters were passed
     */
    function partial(callable $fn)
    {

        $args = \func_get_args();
        $fn = \array_shift($args);

        return function () use($fn, $args) {
            $allArgs = \array_merge($args, \func_get_args());
            return \call_user_func_array($fn, $allArgs);
        };
    }
}
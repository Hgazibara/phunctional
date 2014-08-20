<?php

namespace phunctional\iter {
    
    use phunctional\func as func;
    use phunctional\operators as operators;
    
    /**
     * Tries to check if passed sequence can be used in foreach loop.
     * 
     * @param mixed $sequence sequence which should be checked
     * @return boolean true if it can
     */
    function isIterable($sequence)
    {
        return \is_array($sequence) || $sequence instanceof \Traversable;
    }

    /**
     * Returns elements of sequence satisfying given condition.
     * 
     * @param callable $callback callable defining condition
     * @param mixed $sequence sequence of elements to filter
     * 
     * @return \Generator
     */
    function filter(callable $callback, $sequence)
    {
        foreach($sequence as $item) {
            if(\call_user_func($callback, $item) === TRUE) {
                yield $item;
            }
        }
    }

    /**
     * Returns elements of sequence not satisfying given condition.
     * 
     * @param callback $callback callable definining condition
     * @param mixed $sequence sequence of elements to filter
     * 
     * @return \Generator
     */
    function filterFalse(callable $callback, $sequence)
    {
        foreach($sequence as $item) {
            if(\call_user_func($callback, $item) === false) {
                yield $item;
            }
        }
    }

    /**
     * Applies a function to all elements of a received sequence.
     * 
     * @param callable $callback function which should be applied
     * @param mixed $sequence sequence of elements
     * 
     * @return \Generator
     */
    function map(callable $callback, $sequence)
    {
        foreach($sequence as $item) {
            yield \call_user_func($callback, $item);
        }
    }

    /**
     * Reduces a sequence of elements down to a single element using a specified
     * binary operation.
     * 
     * @param callable $callback desired binary operation
     * @param mixed $sequence sequence of elements
     * @param mixed $start initial value to start with
     * 
     * @return mixed computed single value
     */
    function reduce(callable $callback, $sequence, $start = 0)
    {
        $result = $start;
        foreach ($sequence as $item) {
            $result = \call_user_func($callback, $result, $item);
        }
        return $result;
    }

    /**
     * Computes sum of all elements in a sequence.
     * 
     * @param mixed $sequence sequence of elements
     * @param mixed $start initial value
     * 
     * @return mixed computed sum
     */
    function sum($sequence, $start = 0)
    {
        return $start + reduce(operators\operator('+'), $sequence);
    }

    /**
     * Computes product of all elements in a sequence.
     * 
     * @param number $sequence sequence of elements
     * @param number $start value using which result is multiplied
     * 
     * @return number computed result
     */
    function product($sequence, $start = 1)
    {
        return $start * reduce(operators\operator('*'), $sequence, 1);
    }

    /**
     * Checks is any element of a sequence evaluates to true.
     * 
     * @param mixed $sequence sequence containing elements to check
     * 
     * @return boolean true if at least one element evaluates to true
     */
    function any($sequence)
    {
        foreach ($sequence as $item) {
            if ($item) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if all elements of a sequence evaluate to true.
     * 
     * @param mixed $sequence sequence containing elements to check
     * 
     * @return boolean true if all elements evaluate to true
     */
    function all($sequence)
    {
        foreach ($sequence as $item) {
            if (!$item) {
                return false;
            }
        }

        return true;
    }

    /**
     * Infinitely cycles elements from a received sequence.
     * 
     * @param type $sequence sequence to be used
     * 
     * @return \Generator
     */
    function cycle($sequence)
    {
        $data = [];

        foreach ($sequence as $item) {
            yield $item;
            $data[] = $item;
        }

        while($data) {
            foreach($data as $item) {
                yield $item;
            }
        }
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Produces a sequence of numbers defined by starting and ending points
     * and a step between adjacent elements.
     * 
     * @param numeric $start
     * @param numeric $stop
     * @param numeric $step
     * 
     * @return \Generator
     * 
     * @throws \InvalidArgumentException if step is not a number
     * @throws \LogicException if step's value might produce infinite range
     */
    function range($start, $stop=null, $step = 1)
    {
        if (is_null($stop)) {
            $stop = $start;
            $start = 0;
        }

        if($start < $stop) {

            if($step < 0) {
                throw new \LogicException('Step should be a positive number');
            }

            for ($i = $start; $i <= $stop; $i += $step) {
                yield $i;
            }
        } else {

            if($step >= 0) {
                throw new \LogicException('Step should be a negative number');
            }

            for ($i = $start; $i >= $stop; $i += $step) {
                yield $i;
            }
        }
    }

    /**
     * Creates a sequence with a single element repeated specified number of times.
     * 
     * @param mixed $item element which should be repeated
     * @param integer $times number of times to repeat the element
     * 
     * @return \Generator
     */
    function repeat($item, $times)
    {
        return map(
            func\partial(operators\id(), $item),
            range(1, $times)
        );
    }

    /**
     * Turns string into a sequence which can be traversed.
     * 
     * Method is similar to using preg_split() for turning a string into an array,
     * except it doesn't return an array but creates a generator instead.
     * 
     * @param type $string string to convert
     * @param string $encoding string's encoding
     * 
     * @return \Generator
     */
    function istring($string, $encoding='UTF-8')
    {
        for ($pos = 0, $length = \mb_strlen($string, $encoding); $pos < $length; ++$pos) {
            yield \mb_substr($string, $pos, 1, $encoding);
        }
    }

    /**
     * Creates an iterator which generates elements from received sequence.
     * 
     * @param mixed $sequence sequence from which elements are taken
     */
    function iter($sequence)
    {
        foreach ($sequence as $item) {
            yield $item;
        }
    }

    /**
     * Consumes and returns next element from a received iterator.
     * 
     * @param \Iterator $iterator iterator which should return next element
     * @param mixed $default value to be returned after iterator becomes empty
     * 
     * @return mixed next element
     */
    function next(\Iterator $iterator, $default=null)
    {
        $item = $iterator->current();
        $iterator->next();
        return is_null($item) ? $default : $item;
    }

    /**
     * Turns a received sequence into an array.
     * 
     * @param mixed $target sequence which should be transformed
     * 
     * @return array array containing values from a sequence
     */
    function toArray($target)
    {
        $result = [];

        foreach ($target as $item) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Creates a new sequence of arrays, where i-th array is composed of i-th
     * element from each of the received sequences. 
     * 
     * Since lengths of the received sequences might differ, total length of a 
     * new sequence is determined by the length of the shortest received sequence.
     * 
     * @see zip_longest
     * 
     * @param mixed[] $sequence. one or more sequences
     * 
     * @return \Generator
     */
    function zip()
    {
        $sequences = toArray(map('\phunctional\iter\iter', \func_get_args()));

        while (\func_num_args()) {
            $data = [];
            foreach ($sequences as $sequence) {
                $data[] = next($sequence);
            }
            if(\in_array(null, $data)) {
                break;
            }
            yield $data;
        }
    }

    /**
     * Creates a new sequence of arrays, where i-th array is composed of i-th
     * element from each of the received sequences. 
     * 
     * Total length of a new sequence is determined by the length of the longest
     * received sequence. Since not all received sequences are equally long, values
     * for too short sequences are either null or set to a user-defined value
     * -- $fillValue.
     * 
     * Since method accepts variable number of arguments, to be able to determine
     * if $fillValue was defined, it is necessary that $fillValue is a scalar
     * value and that it is specified as the last argument. Otherwise, the situation
     * would be to ambigious to determine whether $fillValue should be used
     * or not.
     * 
     * By default, $fillValue is set to null.
     * 
     * @see zip
     * 
     * @param mixed[] $sequence,... one or more sequences
     * @param mixed $fillValue value to be used for exhausted iterators
     * 
     * @return \Generator
     */
    function zipLongest()
    {
        $args = \func_get_args();
        $numArgs = \func_num_args();

        if ($numArgs > 1 && (is_scalar($args[$numArgs-1]) || is_null($args[$numArgs-1]))) {
            $fillValue = array_pop($args);
        } else {
            $fillValue = null;
        }

        $sequences = toArray(map('\phunctional\iter\iter', $args));
        $elementLength = count($sequences);

        if ($elementLength) {
            $emptyArray = \array_fill(0, $elementLength, $fillValue);
        }

        while ($elementLength) {
            $data = [];
            foreach ($sequences as $sequence) {
                $data[] = next($sequence, $fillValue);
            }
            if($data === $emptyArray) {
                break;
            }
            yield $data;
        }
    }

    /**
     * Returns a portion of original sequence, defined by the starting point,
     * length and step.
     * 
     * @param mixed $sequence sequence to be sliced
     * @param int $start index of first element to be included
     * @param mixed $length how many elements to include
     * @param int $step how big should the step be
     * 
     * @return \Generator
     */
    function slice($sequence, $start = 0, $length = null, $step = 1)
    {
        $current = 0;
        $length = \is_null($length) ? -1 : $length;

        foreach ($sequence as $item) {
            if($length === 0) {
                break;
            }
            if($current >= $start) {
                yield $item;
                --$length;
            }
            $current += $step;
        }
    }

    /**
     * Creates a new sequence by removing first n elements from the original
     * sequence.
     * 
     * @param int $length how many element should be droped
     * @param mixed $sequence sequence to alter
     * 
     * @return \Generator
     */
    function drop($length, $sequence)
    {
        return slice($sequence, $length);
    }

    /**
     * Generates specified number of elements from the start of iterable.
     * 
     * If a specified sequence doesn't contain enough elements (total number 
     * of elements is less than the desired number), then an empty generator
     * is produced.
     * 
     * @param int $lenth number of elements to use
     * @param mixed $sequence sequence containing target elements
     * 
     * @return \Generator
     * 
     * @throws \InvalidArgumentException in case $iterable can't be iterated over
     */
    function take($lenth, $sequence)
    {
        return slice($sequence, 0, $lenth);
    }

    /**
     * Returns a first element from a sequence.
     * 
     * @param mixed $sequence targeted sequence
     * 
     * @return mixed first element or null
     */
    function first($sequence)
    {
        return slice($sequence, 0, 1)->current();
    }


    /**
     * Retrieves second element from a sequence.
     * 
     * @param mixed $sequence targeted sequence
     * 
     * @return mixed second element or null
     */
    function second($sequence)
    {
        return slice($sequence, 1, 1)->current();
    }

    /**
     * Retrieves last element from a sequence.
     * 
     * @param mixed $sequence sequence containing targeted element
     * 
     * @return mixed last element in a sequence
     */
    function last($sequence)
    {
        return reduce(function ($a, $b) { return $b; }, $sequence, null);
    }

    /**
     * Extracts specified element from a sequence.
     * 
     * @param int $which position of targeted element
     * @param mixed $sequence sequence of elements
     * 
     * @return mixed desired element or null
     */
    function nth($which, $sequence)
    {
        return slice($sequence, $which-1, 1)->current();
    }

    /**
     * Counts the total number of elements in a sequence.
     * 
     * @param mixed $sequence target sequence
     * 
     * @return int number of elements
     */
    function count($sequence)
    {
        return sum(map(function () { return 1; }, $sequence));
    }
}
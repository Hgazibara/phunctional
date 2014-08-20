<?php

namespace phunctional\iter;

use phunctional\func as func;
use phunctional\operators as operators;

/**
 * Description of PhunctionalTest
 *
 * @author Hrvoje Gazibara
 * 
 * For detailed copyright and license information, please consult LICENSE file 
 * distributed with the source code.
 */
class ItertoolslTest extends \PHPUnit_Framework_TestCase {
    
    // Helper functions
    
    private function to2DArray($iterable) {
        $result = [];
        
        foreach ($iterable as $item) {
            $result[] = [$item];
        }
        
        return $result;
    }
    
    private function getSampleArray() {
        return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    }
    
    private function getSampleArrayEven() {
        return [2, 4, 6, 8, 10];
    }
    
    private function getSampleArrayOdd() {
        return [1, 3, 5, 7, 9];
    }
    
    private function getSampleAssocArray() {
        return ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
    }
    
    private function getSomeAscRangeBounds() {
        return [1, 10];
    }
    
    private function getSomeDescRangeBounds() {
        return [10, 1];
    }
    
    private function getZipResult() {
        return [[1, 2], [3, 4], [5, 6], [7, 8], [9, 10]];
    }
    
    private function getZipLongestResult($fillValue=\NULL) {
        return [
            [1, 1], [2, 3], [3, 5], [4, 7], [5, 9],
            [6, $fillValue], [7, $fillValue], [8, $fillValue],
            [9, $fillValue], [10, $fillValue]
        ];
    }
    
    private function getAsciiString() {
        return 'abcdefghijk';
    }
    
    private function getMultiByteString() {
        return 'čćšđžü';
    }
    
    private function getAnyPositiveNumber() {
        return 10;
    }
    
    private function getAnyNumber() {
        return 2.5;
    }
    
    private function getAnyScalar() {
        return $this->getAnyNumber();
    }
    
    private function getAnySequence() {
        return $this->getSampleArray();
    }
    
    private function getEmptySequence() {
        return [];
    }
    
    private function getSequenceOfTruthyValues() {
        return [1, array('0'), \TRUE];
    }
    
    private function getSequenceOfFalsyValues() {
        return [0, '0', \FALSE, array()];
    }
    
    private function getSequenceOfMixedTruthValues() {
        return [0, '0', \FALSE, array(), 1];
    }
    
    private function getDivisibleBy($number) {
        return function ($a) use ($number) { return $a % $number === 0; };
    }
    
    
    private function producesGenerator($actual) {
        $this->assertInstanceOf('\Generator', $actual);
    }
    
    private function shouldReturnElementAtPosition($position, $actual) {
        $this->assertEquals($this->getAnySequence()[$position], $actual);
    }
    
    private function computesCorrectSum($actual, $initialValue=0) {
        $this->assertEquals(
            \array_sum($this->getSampleArray()) + $initialValue,
            $actual
        );
    }
    
    
    // Tests
    
    // Test: filter
    
    public function testFilter_producesOnlyElementsSatisfyingCondition() {
        $this->assertEquals(
            $this->getSampleArrayEven(),
            toArray(
                filter(
                    $this->getDivisibleBy(2),
                    $this->getSampleArray()
                )
            )
        );
    }
    
    // Test: filterFalse
    
    public function testFilterFalse_producesOnlyElementsNotSatisfyingCondition() {
        $this->assertEquals(
            $this->getSampleArrayOdd(),
            toArray(
                filterFalse(
                    $this->getDivisibleBy(2),
                    $this->getSampleArray()
                )
            )
        );
    }
    
    
    // Test: map
    
    public function testMap_appliesFunctionToAllElements() {
        $const = $this->getAnyNumber();
        $this->assertEquals(
            \array_map(
                func\partial(operators\operator('*'), $const),
                $this->getSampleArray()
            ),
            toArray(
                map(
                    func\partial(operators\operator('*'), $const),
                    $this->getSampleArray()
                )
            )
        );
    }
    
    
    // Test: reduce
    
    public function testReduce_reducesSequenceToSingleElement() {
        $this->computesCorrectSum(
            reduce(
                operators\operator('+'),
                $this->getSampleArray()
            )
        );
    }
    
    public function testReduce_allowsDefinitionOfInitialValue() {
        $initialValue = $this->getAnyNumber();
        $this->computesCorrectSum(
            reduce(
                operators\operator('+'),
                $this->getSampleArray(),
                $initialValue
            ), $initialValue
        );
    }
    
    // Test: sum
    
    public function testSum_computesSumOfReceivedElements() {
        $this->computesCorrectSum(sum($this->getSampleArray()));
    }
    
    public function testSum_allowsDefinitionOfInitialValue() {
        $initialValue = $this->getAnyPositiveNumber();
        $this->computesCorrectSum(
            sum($this->getSampleArray(), $initialValue),
            $initialValue
        );
    }


    // Test: product
    
    public function testProduct_computesProductOfReceivedElements() {
        $this->computesCorrectProduct($this->getSampleArray());
    }
    
    public function testProduct_canMultiplyResultBySomeValue() {
        $this->computesCorrectProduct(
            $this->getSampleArray(),
            $this->getAnyNumber()
        );
    }
    
    private function computesCorrectProduct($array, $factor=1) {
        $this->assertEquals(
            \array_reduce($array, operators\operator('*'), $factor),
            product($array, $factor)
        );
    }
    
    
    // Test: any
    
    public function testAny_returnsTrueIfAnyElementIsTruthy() {
        $this->assertTrue(any($this->getSequenceOfMixedTruthValues()));
    }
    
    public function testAny_returnsFalseOnlyIfAllElementsAreFalsy() {
        $this->assertFalse(any($this->getSequenceOfFalsyValues()));
    }
    
    
    // Test: all
    
    public function testAll_returnsTrueIfAllElementsAreTruthy() {
        $this->assertTrue(all($this->getSequenceOfTruthyValues()));
    }
    
    public function testAll_returnsFalseIfAtLeastOneElementIsFalsy() {
        $this->assertFalse(all($this->getSequenceOfMixedTruthValues()));
    }
    
    // Test: istring
    
    public function testIString_createsTraversableSequenceFromString() {
        $generator = istring($this->getAsciiString());
        $this->producesGenerator($generator);
        $this->assertEquals(
            \str_split($this->getAsciiString()),
            toArray($generator)
        );
    }
    
    public function testIString_supportsMultibyteCharacters() {
        $generator = istring($this->getMultiByteString());
        $this->producesGenerator($generator);
        $this->assertEquals(
            \preg_split('//u', $this->getMultiByteString(), -1, PREG_SPLIT_NO_EMPTY),
            toArray($generator)
        );
    }
    
    
    // Test: iter
    
    public function testIter_createsGeneratorFromIterableSequence() {
        $this->producesGenerator(iter($this->getSampleArray()));
        $this->producesGenerator(iter($this->getAsciiString()));
        $this->producesGenerator(iter(
            range($this->getAnyPositiveNumber())
        ));
    }
    
    
    // Test: next
    
    public function testNext_retrievesNextElementFromIterator() {
        $iterator = iter($this->getAnySequence());
        $length = count($this->getAnySequence());
        for ($i = 0; $i < $length; ++$i) {
            $this->shouldReturnElementAtPosition($i, next($iterator));
        }
    }
    
    public function testNext_returnsNullForEmptySequence() {
        $this->returnsCorrectDefault(next(
            iter($this->getEmptySequence())
        ));
    }
    
    public function testNext_returnValueForEmptySequenceCanBeChanged() {
        $this->returnsCorrectDefault(
            next(
                iter($this->getEmptySequence()),
                $this->getAnyScalar()
            ),
            $this->getAnyScalar()
        );
    }
    
    private function returnsCorrectDefault($actual, $expected=\NULL) {
        $this->assertEquals($expected, $actual);
    }
    
    
    // Test: zip
    
    public function testZip_acceptsSingleSequence() {
        $generator = zip($this->getSampleArray());
        
        $this->producesGenerator($generator);
        $this->assertEquals(
            $this->to2DArray($this->getSampleArray()),
            toArray($generator)
        );
    }
    
    public function testZip_acceptsMultipleSequences() {
        $generator = zip(
            $this->getSampleArrayOdd(),
            $this->getSampleArrayEven()
        );
        
        $this->producesGenerator($generator);
        $this->assertEquals(
            $this->getZipResult(),
            toArray($generator)
        );
    }
    
    public function testZip_sequenceLengthIsDeterminedByShortestSequence() {
        $generator = zip(
            $this->getSampleArray(),
            $this->getSampleArrayOdd()
        );
        
        $this->assertTrue(
            count($generator) === count($this->getSampleArrayOdd())
        );
    }
    
    // Test: zipLongest
    
    public function testZipLongest_acceptsSingleSequence() {
        $generator = zipLongest($this->getSampleArray());
        
        $this->producesGenerator($generator);
        $this->assertEquals(
            $this->to2DArray($this->getSampleArray()),
            toArray($generator)
        );
    }
    
    public function testZipLongest_acceptsMultipleSequences() {
        $generator = zipLongest(
            $this->getSampleArray(),
            $this->getSampleArrayOdd()
        );
        
        $this->producesGenerator($generator);
        $this->assertEquals(
            $this->getZipLongestResult(),
            toArray($generator)
        );
    }
    
    public function testZipLongest_sequenceLengthIsDeterminedByLongestSequence() {
        $generator = zipLongest(
            $this->getSampleArray(),
            $this->getSampleArrayOdd()
        );
        
        $this->assertTrue(
            count($generator) === count($this->getSampleArray())
        );
    }
    
    public function testZipLongest_missingValueCanBeReplacedByAnyScalar() {
        $scalar = $this->getAnyScalar();
        $generator = zipLongest(
            $this->getSampleArray(),
            $this->getSampleArrayOdd(),
            $scalar
        );
        
        $this->assertEquals(
            $this->getZipLongestResult($scalar),
            toArray($generator)
        );
    }
    
    
    // Test: toArray
    
    public function testToArray_convertsSequenceToArray() {
        $this->assertEquals(
            toArray($this->getAnySequence()), // !!!!!!!!!!!!!!!!!!
            toArray($this->getAnySequence())
        );
    }
    
    
    // Test: isIterable
    
    public function testIsIterable_checksIfArgumentIsArrayOrTraversable() {
        $this->assertTrue(isIterable(
            range($this->getAnyPositiveNumber())
        ));
        $this->assertTrue(isIterable($this->getSampleArray()));
        $this->assertFalse(isIterable($this->getAsciiString()));
    }
    
    
    // Test: range
    
    public function testRange_producesSequenceWithInclusiveBounds() {
        list($start, $stop) = $this->getSomeAscRangeBounds();
        $this->checkIfProducesCorrectRange($start, $stop);
    }
    
    public function testRange_allowsArbitraryNumericStep() {
        list($start, $stop) = $this->getSomeAscRangeBounds();
        $step = $this->getAnyNumber();
        $this->checkIfProducesCorrectRange($start, $stop, $step);
    }
    
    public function testRange_canProduceDescendingSequence() {
        list($start, $stop) = $this->getSomeDescRangeBounds();
        $step = $this->getAnyNumber();
        $this->checkIfProducesCorrectRange($start, $stop, -1*$step);
    }
    
    public function testRange_rangeStartsFromZeroIfStopNotDefined() {
        $this->assertEquals(
            0,
            range($this->getAnyPositiveNumber())->current()
        );
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testRange_throwsExceptionIfNegativeStepMightCreateInfiniteRange() {
        list($start, $stop) = $this->getSomeAscRangeBounds();
        range($start, $stop, -1*$this->getAnyPositiveNumber())->current();
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testRange_throwsExceptionIfPositiveStepMightCreateInfiniteRange() {
        list($start, $stop) = $this->getSomeDescRangeBounds();
        range($start, $stop, $this->getAnyPositiveNumber())->current();
    }
    
    private function checkIfProducesCorrectRange($start, $stop=NULL, $step=1) {
        $this->assertEquals(
            \range($start, $stop, $step),
            toArray(range($start, $stop, $step))
        );
    }
    
    
    // Test: repeat
    
    public function testRepeat_repeatsAnyElementGivenNumberOfTimes() {
        list($value, $times) = [$this->getAnyScalar(), $this->getAnyPositiveNumber()];
        $this->assertEquals(
            \array_fill(0, $times, $value),
            toArray(repeat($value, $times))
        );
    }
    
    
    // Test: slice
    
    public function testSlice_returnsPortionOfSequence() {
        list($start, $length) = [2, 5];
        $this->assertEquals(
            \array_slice($this->getSampleArray(), $start, $length),
            toArray(slice($this->getSampleArray(), $start, $length))
        );
    }
    
    public function testSlice_returnsAllElementsIfSequenceLengthLessThanSpecified() {
        $input = $this->getAnySequence();
        $length = count($input);
        $sequence = slice($input, 0, $length + 1);
        
        $this->assertEquals($length, count($sequence));
    }
    
    public function testSlice_doesntPreserveAssocArrayKeys() {
        $start = 1;
        $this->assertEquals(
            \array_slice(\array_values($this->getSampleAssocArray()), $start),
            toArray(slice($this->getSampleAssocArray(), $start))
        );
    }
    
    public function testSlice_supportsAnyTypeOfSequence() {
        $start = 1;
        $generator = slice($this->getAnySequence(), $start);
        
        $this->producesGenerator($generator);
        $this->assertEquals(
            \array_slice(toArray($this->getAnySequence()), $start),
            toArray($generator)
        );
    }
    
    public function testSlice_canWorkWithGenerators() {
        $start = 1;
        $sequence = slice(
            range($this->getAnyPositiveNumber()), $start
        );
        
        $this->producesGenerator($sequence);
        $this->assertEquals(
            \array_slice(\range(0, $this->getAnyPositiveNumber()), $start),
            toArray($sequence)
        );
    }
    
    
    // Test: drop
    
    public function testDrop_removesFirstNElementsFromSequence() {
        $this->assertEquals(
            \array_slice(
                $this->getSampleArray(),
                \count($this->getSampleArray()) - 1
            ),
            toArray(drop(
                \count($this->getSampleArray()) - 1,
                $this->getSampleArray()
            ))
        );
    }

    public function testDrop_dropsAllElementsIfSpecifiedLengthTooBig() {
        $this->assertEquals(
            [],
            toArray(
                drop(\count($this->getSampleArray()) + 1,
                $this->getSampleArray())
            )
        );
    }
    

    // Test: take
    
    public function testTake_retrievesSpecifiedNumberOfElementsFromTheTop() {
        $this->checkTakeOutput(
            $this->getSampleArray(),
            \count($this->getSampleArray()) - 1,
            \count($this->getSampleArray()) - 1
        );
    }
    
    public function testTake_retrievesAllElementsIfSequenceTooShort() {
        $this->checkTakeOutput(
            $this->getSampleArray(),
            \count($this->getSampleArray()) + 1,
            \count($this->getSampleArray())
        );
    }
    
    public function testTake_createsEmptySequenceForEmptyInputSequence() {
        $this->assertEquals(
            [],
            toArray(take(
                $this->getAnyPositiveNumber(),
                $this->getEmptySequence()
            ))
        );
    }
    
    private function checkTakeOutput($testArray, $howMany, $expectedSize) {        
        $elements = toArray(take($howMany, $testArray));
        $this->assertCount($expectedSize, $elements);
        $this->assertEquals(array_slice($testArray, 0, $expectedSize), $elements);
    }
    
    
    // Test: first
    
    public function testFirst_returnsFirstElementFromSequence() {
        $this->shouldReturnElementAtPosition(
            0,
            first($this->getAnySequence())
        );
    }
    
    public function testFirst_returnsNullIfSequenceIsEmpty() {
        $this->assertNull(first($this->getEmptySequence()));
    }
    
    
    // Test: second
    
    public function testSecond_returnsSecondElementFromSequence() {
        $this->shouldReturnElementAtPosition(
            1,
            second($this->getAnySequence())
        );
    }
    
    public function testSecond_returnsNullIfSequnceIsTooShort() {
        $this->assertNull(second([]));
    }
    
    
    // Test: last
    
    public function testLast_returnsLastElementFromSequence() {
        $this->shouldReturnElementAtPosition(
            count($this->getAnySequence())-1,
            last($this->getAnySequence())
        );
    }
    
    public function testLast_returnsNullIfSequenceIsEmpty() {
        $this->assertNull(last($this->getEmptySequence()));
    }
    
    
    // Test: nth
    
    public function testNth_returnsElementAtSpecifiedPosition() {
        foreach (range(1, count($this->getAnySequence())) as $pos) {
            $this->shouldReturnElementAtPosition(
                $pos-1,
                nth($pos, $this->getAnySequence())
            );
        }
    }
    
    public function testNth_returnsNullIfSequenceHasTooFewElements() {
        $this->assertNull(nth(
            count($this->getAnySequence()) + 1,
            $this->getAnySequence()
        ));
    }

    
    // Test: count
    
    public function testCount_countsNumberOfElementsInSequence() {
        $this->assertEquals(
            \count($this->getSampleArray()),
            count($this->getSampleArray())
        );
    }
    
    public function testCount_worksCorrectlyWithMultibyteStrings() {
        $this->assertEquals(
            \mb_strlen($this->getMultiByteString(), 'UTF-8'),
            count(istring($this->getMultiByteString()))
        );
    }
    

    // Test: cycle
    
    public function testCycle_infinitelyCyclesElements() {
        $this->assertEquals(
            [1, 2, 3, 1, 2, 3, 1, 2, 3],
            toArray(take(9, cycle([1, 2, 3])))                
        );
    }

}

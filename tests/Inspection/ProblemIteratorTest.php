<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Inspection\Core\Inspection\Problem;
use Scheb\Inspection\Core\Inspection\ProblemFactory;
use Scheb\Inspection\Core\Inspection\ProblemIterator;
use Scheb\Inspection\Core\Inspection\ProblemXmlIterator;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemIteratorTest extends TestCase
{
    private const INSPECTION_FILE = 'InspectionFile.xml';
    private const PROJECT_ROOT = '/project/root';

    /**
     * @test
     */
    public function getAggregator_iterateXmlFile_yieldAllProblems(): void
    {
        $problem1 = "<problem><file>file1</file>\n</problem>";
        $problem2 = '<problem><file>file2</file></problem>';
        $problem3 = '<problem><file>file3</file></problem>';
        $xmlIterator = $this->createXmlIterator([$problem1, $problem2, $problem3]);

        $problem = $this->createMock(Problem::class);

        $problemFactory = $this->createMock(ProblemFactory::class);
        $problemFactory
            ->expects($this->exactly(3))
            ->method('create')
            ->withConsecutive(
                [self::PROJECT_ROOT, self::INSPECTION_FILE, $problem1],
                [self::PROJECT_ROOT, self::INSPECTION_FILE, $problem2],
                [self::PROJECT_ROOT, self::INSPECTION_FILE, $problem3]
            )
            ->willReturn($problem);

        $iterator = new ProblemIterator($xmlIterator, $problemFactory, self::PROJECT_ROOT);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(3, $result);
    }

    /**
     * @return ProblemXmlIterator|MockObject
     */
    private function createXmlIterator(array $problems): MockObject
    {
        $iterator = $this->createMock(ProblemXmlIterator::class);
        $iterator
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($problems));

        $iterator
            ->expects($this->any())
            ->method('getInspectionFilePath')
            ->willReturn('InspectionFile.xml');

        return $iterator;
    }
}

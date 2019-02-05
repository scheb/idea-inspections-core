<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Inspection\Core\Inspection\Problem;
use Scheb\Inspection\Core\Inspection\ProblemAggregator;
use Scheb\Inspection\Core\Inspection\ProblemFactory;
use Scheb\Inspection\Core\Inspection\ProblemIterator;
use Scheb\Inspection\Core\Inspection\ProblemIteratorFactory;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemAggregatorTest extends TestCase
{
    private const PROJECT_PATH = '/project/path';

    /**
     * @var ProblemIteratorFactory|MockObject
     */
    private $problemIteratorFactory;

    /**
     * @var ProblemFactory|MockObject
     */
    private $problemFactory;

    /**
     * @var ProblemAggregator
     */
    private $problemAggregator;

    protected function setUp()
    {
        $this->problemIteratorFactory = $this->createMock(ProblemIteratorFactory::class);
        $this->problemFactory = $this->createMock(ProblemFactory::class);
        $this->problemAggregator = new ProblemAggregator($this->problemIteratorFactory, $this->problemFactory);
    }

    /**
     * @return MockObject|ProblemIterator
     */
    private function createProblemIteratorReturningXml(array $problems): MockObject
    {
        $iterator = $this->createMock(ProblemIterator::class);
        $iterator
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($problems));

        return $iterator;
    }

    private function stubProblemIteratorFactoryCreatesIterator(array $inspectionIterators): void
    {
        $map = [];
        foreach ($inspectionIterators as $inspection => $iterator) {
            $map[] = [$inspection, $iterator];
        }

        $this->problemIteratorFactory
            ->expects($this->any())
            ->method('createProblemIterator')
            ->willReturnMap($map);
    }

    /**
     * @test
     */
    public function readInspections_iterateMultipleFiles_createProblems(): void
    {
        $iteratorFile1 = $this->createProblemIteratorReturningXml(['xml1', 'xml2']);
        $iteratorFile2 = $this->createProblemIteratorReturningXml(['xml3']);

        $this->stubProblemIteratorFactoryCreatesIterator([
            'file1' => $iteratorFile1,
            'file2' => $iteratorFile2,
        ]);

        $this->problemFactory
            ->expects($this->exactly(3))
            ->method('create')
            ->withConsecutive(
                [self::PROJECT_PATH, 'file1', 'xml1'],
                [self::PROJECT_PATH, 'file1', 'xml2'],
                [self::PROJECT_PATH, 'file2', 'xml3']
            )
            ->willReturn($this->createMock(Problem::class));

        $summary = $this->problemAggregator->readInspections(['file1', 'file2'], self::PROJECT_PATH);
        $this->assertEquals(3, $summary->getNumProblems());
    }
}

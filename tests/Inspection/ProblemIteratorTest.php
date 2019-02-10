<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Inspection\Core\Inspection\ProblemIterator;
use Scheb\Inspection\Core\Inspection\ProblemXmlIterator;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemIteratorTest extends TestCase
{
    private const PROBLEM1_XML = '<problem><file>file1</file><description>description1</description><problem_class severity="warning"/></problem>';
    private const PROBLEM2_XML = '<problem><file>file2</file><description>description2</description><problem_class severity="error"/></problem>';
    private const PROBLEM3_XML = '<problem><file>file3</file><description>description3</description><problem_class severity="warning"/></problem>';

    private function createProblemIterator(ProblemXmlIterator $problemXmlIterator, array $ignoreInspections, array $ignoreFiles, array $ignoreMessages, array $ignoreSeverity): ProblemIterator
    {
        return new ProblemIterator($problemXmlIterator, $ignoreInspections, $ignoreFiles, $ignoreMessages, $ignoreSeverity);
    }

    /**
     * @return ProblemXmlIterator|MockObject
     */
    private function createXmlIterator(): MockObject
    {        $iterator = $this->createMock(ProblemXmlIterator::class);
        $iterator
            ->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([
                self::PROBLEM1_XML,
                self::PROBLEM2_XML,
                self::PROBLEM3_XML
            ]));

        $iterator
            ->expects($this->any())
            ->method('getInspectionFilePath')
            ->willReturn('InspectionFile1.xml');

        return $iterator;
    }

    /**
     * @test
     */
    public function getAggregator_ignoredInspections_returnNoProblems(): void
    {
        $xmlIterator = $this->createXmlIterator();

        $iterator = $this->createProblemIterator($xmlIterator, ['InspectionFile1'], [], [], []);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function getAggregator_ignoreFile1_returnProblem2And3(): void
    {
        $xmlIterator = $this->createXmlIterator();
        $iterator = $this->createProblemIterator($xmlIterator, [], ['file[0-1]'], [], []);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(2, $result);
        $this->assertContains(self::PROBLEM2_XML, $result);
        $this->assertContains(self::PROBLEM3_XML, $result);
    }

    /**
     * @test
     */
    public function getAggregator_ignoreMessage1_returnProblem1(): void
    {
        $xmlIterator = $this->createXmlIterator();
        $iterator = $this->createProblemIterator($xmlIterator, [], [], ['description[2-3]'], []);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(1, $result);
        $this->assertContains(self::PROBLEM1_XML, $result);
    }

    /**
     * @test
     */
    public function getAggregator_ignoreSeverityError_returnProblem1And3(): void
    {
        $xmlIterator = $this->createXmlIterator();
        $iterator = $this->createProblemIterator($xmlIterator, [], [], [], ['error']);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(2, $result);
        $this->assertContains(self::PROBLEM1_XML, $result);
        $this->assertContains(self::PROBLEM3_XML, $result);
    }
}

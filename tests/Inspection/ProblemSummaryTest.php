<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Inspection\Core\Inspection\Problem;
use Scheb\Inspection\Core\Inspection\ProblemSummary;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemSummaryTest extends TestCase
{
    /**
     * @var ProblemSummary
     */
    private $problemSummary;

    protected function setUp()
    {
        $this->problemSummary = new ProblemSummary();
        $this->problemSummary->addProblem('InspectionFile1.xml', 'file1', $this->createProblem());
        $this->problemSummary->addProblem('InspectionFile1.xml', 'file2', $this->createProblem());
        $this->problemSummary->addProblem('InspectionFile2.xml', 'file1', $this->createProblem());
        $this->problemSummary->addProblem('InspectionFile2.xml', 'file2', $this->createProblem());
        $this->problemSummary->addProblem('InspectionFile2.xml', 'file3', $this->createProblem());
    }

    /**
     * @return Problem|MockObject
     */
    private function createProblem(): MockObject
    {
        return $this->createMock(Problem::class);
    }

    /**
     * @test
     */
    public function getProblemsByFile_problemsAdded_returnPerFile(): void
    {
        $problemsByFile = $this->problemSummary->getProblemsByFile();

        $this->assertArrayHasKey('file1', $problemsByFile);
        $this->assertCount(2, $problemsByFile['file1']);

        $this->assertArrayHasKey('file2', $problemsByFile);
        $this->assertCount(2, $problemsByFile['file2']);

        $this->assertArrayHasKey('file3', $problemsByFile);
        $this->assertCount(1, $problemsByFile['file3']);
    }

    /**
     * @test
     */
    public function getProblemsByInspection_problemsAdded_returnPerInspection(): void
    {
        $problemsByInspection = $this->problemSummary->getProblemsByInspection();

        $this->assertArrayHasKey('InspectionFile1.xml', $problemsByInspection);
        $this->assertCount(2, $problemsByInspection['InspectionFile1.xml']);

        $this->assertArrayHasKey('InspectionFile2.xml', $problemsByInspection);
        $this->assertCount(3, $problemsByInspection['InspectionFile2.xml']);
    }

    /**
     * @test
     */
    public function getNumProblems_problemsAdded_returnProblemCount(): void
    {
        $this->assertEquals(5, $this->problemSummary->getNumProblems());
    }

    /**
     * @test
     */
    public function getNumProblems_problemsAdded_returnFilesCount(): void
    {
        $this->assertEquals(3, $this->problemSummary->getNumFiles());
    }

    /**
     * @test
     */
    public function getNumInspections_problemsAdded_returnProblemCount(): void
    {
        $this->assertEquals(2, $this->problemSummary->getNumInspections());
    }
}

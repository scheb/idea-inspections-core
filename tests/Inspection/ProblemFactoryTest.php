<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use Scheb\Inspection\Core\Inspection\Problem;
use Scheb\Inspection\Core\Inspection\ProblemFactory;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemFactoryTest extends TestCase
{
    /**
     * @var ProblemFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new ProblemFactory();
    }

    /**
     * @test
     */
    public function create_xmlGiven_returnProblem(): void
    {
        $xmlElement = $this->loadXml();
        $createdProblem = $this->factory->create('/path/to/project/', '/inspections/InspectionName.xml', $xmlElement);

        $expectedProblem = new Problem('InspectionName', '/path/to/project/src/file', 123, 'Problem class', 'WARNING', 'Description >');
        $this->assertEquals($expectedProblem, $createdProblem);
    }

    /**
     * @test
     */
    public function create_noProjectRoot_useRelativePath(): void
    {
        $xmlElement = $this->loadXml();
        $createdProblem = $this->factory->create('', '/inspections/InspectionName.xml', $xmlElement);

        $expectedProblem = new Problem('InspectionName', 'src/file', 123, 'Problem class', 'WARNING', 'Description >');
        $this->assertEquals($expectedProblem, $createdProblem);
    }

    private function loadXml(): string
    {
        return file_get_contents(__DIR__.'/_fixtures/problem.xml');
    }
}

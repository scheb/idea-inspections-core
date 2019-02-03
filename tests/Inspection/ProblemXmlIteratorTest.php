<?php

namespace Scheb\Inspection\Core\Test\Inspection;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Inspection\Core\FileSystem\FileReader;
use Scheb\Inspection\Core\Inspection\ProblemXmlIterator;
use Scheb\Inspection\Core\Test\TestCase;

class ProblemXmlIteratorTest extends TestCase
{
    private const INSPECTION_FILE = 'InspectionFile.xml';

    /**
     * @test
     */
    public function getAggregator_iterateXmlFile_yieldAllProblems(): void
    {
        $reader = $this->createReader();

        $problem1 = "<problem><file>file1</file>\n</problem>";
        $problem2 = '<problem><file>file2</file></problem>';
        $problem3 = '<problem><file>file3</file></problem>';

        $iterator = new ProblemXmlIterator($reader);
        $result = iterator_to_array($iterator, false);

        $this->assertCount(3, $result);
        $this->assertEquals([$problem1, $problem2, $problem3], $result);
    }

    /**
     * @return FileReader|MockObject
     */
    private function createReader(): MockObject
    {
        $xml = <<<XML
        <problems>

<problem><file>file1</file>
</problem>
        <problem><file>file2</file></problem>

<problem><file>file3</file></problem>

</problems>
XML;
        $chunks = str_split($xml, 5);
        $numChunks = count($chunks);
        $eofResponse = array_fill(0, $numChunks, false);
        $eofResponse[] = true; // EOF reached

        $reader = $this->createMock(FileReader::class);
        $reader
            ->expects($this->any())
            ->method('eof')
            ->willReturnOnConsecutiveCalls(...$eofResponse);
        $reader
            ->expects($this->exactly(count($chunks)))
            ->method('read')
            ->willReturnOnConsecutiveCalls(...$chunks);
        $reader
            ->expects($this->any())
            ->method('getFilePath')
            ->willReturn(self::INSPECTION_FILE);

        return $reader;
    }
}

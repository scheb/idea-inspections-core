<?php

namespace Scheb\Inspection\Core\Inspection;

use Scheb\Inspection\Core\FileSystem\FileReader;

class ProblemIteratorFactory
{
    /**
     * @var string[]
     */
    private $ignoreInspections;

    /**
     * @var string[]
     */
    private $ignoreFiles;

    /**
     * @var string[]
     */
    private $ignoreMessages;

    public function __construct(array $ignoreInspections, array $ignoreFiles, array $ignoreMessages)
    {
        $this->ignoreInspections = $ignoreInspections;
        $this->ignoreFiles = $ignoreFiles;
        $this->ignoreMessages = $ignoreMessages;
    }

    public function createProblemIterator(string $inspectionsFile): ProblemIterator
    {
        return new ProblemIterator(
            new ProblemXmlIterator(new FileReader($inspectionsFile)),
            $this->ignoreInspections,
            $this->ignoreFiles,
            $this->ignoreMessages
        );
    }
}

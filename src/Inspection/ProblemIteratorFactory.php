<?php

namespace Scheb\Inspection\Core\Inspection;

use Scheb\Inspection\Core\FileSystem\FileReader;

class ProblemIteratorFactory
{
    public function createProblemIterator(string $inspectionsFile, string $projectRoot): ProblemIterator
    {
        return new ProblemIterator(new FileReader($inspectionsFile), new ProblemFactory(), $projectRoot);
    }
}

<?php

namespace Scheb\Inspection\Core\Inspection;

class ProblemSummary
{
    /**
     * @var int
     */
    private $numFiles;

    /**
     * @var int
     */
    private $numProblems;

    /**
     * @var int
     */
    private $numInspections;

    /**
     * @var Problem[][]
     */
    private $problemsPerFile = [];

    /**
     * @var Problem[][]
     */
    private $problemsPerInspection = [];

    public function addProblem(string $inspectionsFile, string $fileName, Problem $problem): void
    {
        if (!isset($this->problemsPerInspection[$inspectionsFile])) {
            $this->problemsPerInspection[$inspectionsFile] = [];
            ++$this->numInspections;
        }
        if (!isset($this->problemsPerFile[$fileName])) {
            $this->problemsPerFile[$fileName] = [];
            ++$this->numFiles;
        }
        $this->problemsPerInspection[$inspectionsFile][] = $problem;
        $this->problemsPerFile[$fileName][] = $problem;
        ++$this->numProblems;
    }

    public function getNumFiles(): int
    {
        return $this->numFiles;
    }

    public function getNumProblems(): int
    {
        return $this->numProblems;
    }

    public function getNumInspections(): int
    {
        return $this->numInspections;
    }

    /**
     * @return Problem[][]
     */
    public function getProblemsByInspection(): array
    {
        return $this->problemsPerInspection;
    }

    /**
     * @return Problem[][]
     */
    public function getProblemsByFile(): array
    {
        return $this->problemsPerFile;
    }
}

<?php

namespace Scheb\Inspection\Core\Inspection;

class ProblemAggregator
{
    /**
     * @var ProblemIteratorFactory
     */
    private $problemIteratorFactory;

    /**
     * @var ProblemFactory
     */
    private $problemFactory;

    public function __construct(ProblemIteratorFactory $problemIteratorFactory, ProblemFactory $problemFactory)
    {
        $this->problemIteratorFactory = $problemIteratorFactory;
        $this->problemFactory = $problemFactory;
    }

    public function readInspections(array $inspectionsFiles, string $projectRoot): ProblemSummary
    {
        $problemSummary = new ProblemSummary();
        foreach ($inspectionsFiles as $inspectionsFile) {
            $problemXmlIterator = $this->problemIteratorFactory->createProblemIterator($inspectionsFile);
            foreach ($problemXmlIterator as $problemXml) {
                $problem = $this->problemFactory->create($projectRoot, $inspectionsFile, $problemXml);
                $problemSummary->addProblem($inspectionsFile, $problem);
            }
        }

        return $problemSummary;
    }
}

<?php

namespace Scheb\Inspection\Core\Inspection;

class ProblemIterator implements \IteratorAggregate
{
    /**
     * @var ProblemXmlIterator
     */
    private $iterator;

    /**
     * @var ProblemFactory
     */
    private $problemFactory;

    /**
     * @var string
     */
    private $projectRoot;

    public function __construct(ProblemXmlIterator $iterator, ProblemFactory $problemFactory, string $projectRoot)
    {
        $this->iterator = $iterator;
        $this->problemFactory = $problemFactory;
        $this->projectRoot = $projectRoot;
    }

    public function getIterator(): \Traversable
    {
        $inspectionFile = $this->iterator->getInspectionFilePath();
        foreach ($this->iterator as $problemXml) {
            yield $this->problemFactory->create($this->projectRoot, $inspectionFile, $problemXml);
        }
    }
}

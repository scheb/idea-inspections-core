<?php

namespace Scheb\Inspection\Core\Inspection;

class ProblemIterator implements \IteratorAggregate
{
    /**
     * @var ProblemXmlIterator
     */
    private $problemXmlIterator;

    /**
     * @var string[]
     */
    private $ignoreInspectionsRegex;

    /**
     * @var string[]
     */
    private $ignoreFilesRegex;

    /**
     * @var string[]
     */
    private $ignoreMessagesRegex;

    /**
     * @var array
     */
    private $ignoreSeverity;

    public function __construct(ProblemXmlIterator $problemXmlIterator, array $ignoreInspections, array $ignoreFiles, array $ignoreMessages, array $ignoreSeverity)
    {
        $this->problemXmlIterator = $problemXmlIterator;
        $this->ignoreInspectionsRegex = array_map([$this, 'createRegex'], $ignoreInspections);
        $this->ignoreFilesRegex = array_map([$this, 'createRegex'], $ignoreFiles);
        $this->ignoreMessagesRegex = array_map([$this, 'createRegex'], $ignoreMessages);
        $this->ignoreSeverity = $ignoreSeverity;
    }

    public function getIterator(): \Traversable
    {
        if ($this->matchRegex($this->problemXmlIterator->getInspectionFilePath(), $this->ignoreInspectionsRegex)) {
            return;
        }

        foreach ($this->problemXmlIterator as $problemXml) {
            $problemXmlElement = new \SimpleXMLElement($problemXml);
            $description = $problemXmlElement->description ?? '';
            $fileName = $problemXmlElement->file ?? '';
            $severity = $problemXmlElement->problem_class['severity'] ?? '';

            if ($this->matchRegex($fileName, $this->ignoreFilesRegex)) {
                continue;
            }
            if ($this->matchRegex($description, $this->ignoreMessagesRegex)) {
                continue;
            }
            if (in_array($severity, $this->ignoreSeverity)) {
                continue;
            }

            yield $problemXml;
        }
    }

    private function createRegex(string $pattern): string
    {
        return '#'.str_replace('#', '\\#', $pattern).'#';
    }

    private function matchRegex(string $value, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }
}

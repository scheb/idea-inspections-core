<?php

namespace Scheb\Inspection\Core\Inspection;

class ProblemFactory
{
    public function create(string $projectRoot, string $xmlFilename, string $problemXml): Problem
    {
        $projectRoot = rtrim($projectRoot, '\\/');
        if ($projectRoot) {
            $projectRoot .= '/';
        }

        $problemXml = new \SimpleXMLElement($problemXml);
        $problem = new Problem(
            $this->getInspectionName($xmlFilename),
            $this->getFilename($projectRoot, $problemXml),
            (int) $problemXml->line,
            (string) $problemXml->problem_class,
            (string) $problemXml->problem_class['severity'],
            html_entity_decode((string) $problemXml->description)
        );

        return $problem;
    }

    private function getInspectionName(string $xmlFilename): string
    {
        preg_match('#(\w*)\.xml$#', $xmlFilename, $match);

        return $match[1] ?? 'UNKNOWN';
    }

    private function getFilename(string $projectRoot, \SimpleXMLElement $problemXml): string
    {
        return str_replace('file://$PROJECT_DIR$/', $projectRoot, (string) $problemXml->file);
    }
}

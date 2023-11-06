<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/File/FileGetter.php';
require_once __DIR__ . '/Format/Formatable.php';
require_once __DIR__ . '/Format/PerMonthFormatter.php';

final class Analyzer {
    private FileGetter $fileGetter;

    private Formatable $formatter;

    private Extractable $extractor;

    private string $responsePath;

    public function __construct(FileGetter $fileGetter, Formatable $formatter, Extractable $extractor, string $responsePath) {
        $this->fileGetter = $fileGetter;
        $this->formatter = $formatter;
        $this->extractor = $extractor;
        $this->responsePath = $responsePath;
    }

    /**
     * @throws JsonException
     */
    public function compute(): string {
        $files = $this->fileGetter->get($this->responsePath);
        $contentObjects = $this->getAsObjects($files);

        return $this->formatter->format($this->extractor->extract($contentObjects));
    }

    private function getAsObjects(array $files): array {
        $contentObjects = [];

        foreach ($files as $filePath) {
            $fileContent = file_get_contents($filePath);
            $contentObject = json_decode($fileContent, false, 512, JSON_THROW_ON_ERROR);
            $contentObjects[] = $contentObject;
        }

        return $contentObjects;
    }
}

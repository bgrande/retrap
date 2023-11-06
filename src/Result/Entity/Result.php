<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/NoDataException.php';

final class Result {
    public array $questions = [];

    public int $version = 0;

    public int $result = 0;

    public string $resultText = '';

    public string $language = '';

    public string $date = '';

    public string $from = '';

    public string $fromId = '';

    public function __construct(array $questions, string $resultText, int $version, int $result, string $lang, string $textFrom, string $fromId) {
        if (empty($questions) || $resultText === '') {
            throw new NoAnalyticDataException();
        }

        $dateTime = new DateTime();

        $this->questions  = $questions;
        $this->resultText = $resultText;
        $this->version    = $version;
        $this->result     = $result;
        $this->language   = $lang;
        $this->date       = $dateTime->format('c');
        $this->from       = $textFrom;
        $this->fromId     = $fromId;
    }
}

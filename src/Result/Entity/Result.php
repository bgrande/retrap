<?php

declare(strict_types = 1);

final class Result {
    public $questions = [];

    public $version = 0;

    public $result = 0;

    public $resultText = '';

    public $language = '';

    public $date = '';

    public $from = '';

    public $fromId = '';

    public function __construct(array $questions, string $resultText, int $version, int $result, string $lang, string $textFrom, string $fromId) {
        if (empty($questions) || $resultText === '') {
            throw new NoDataException();
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

<?php

declare(strict_types = 1);

class AnswerUpdater {
    private const ANSWER_Q_KEY = 'answer-q-';

    private $questions;

    private $objectAnswers;

    public function __construct(\stdClass $questions, \stdClass $objectAnswers) {
        $this->questions = $questions;
        $this->objectAnswers = $objectAnswers;
    }

    public function update(): void {
        if (isset($this->questions->{QUESTION_KEY})) {
            $this->loopQuestions($this->questions->{QUESTION_KEY});
        }
    }

    private function loopQuestions(array $questions): void {
        foreach ($questions as $key => $question) {
            $this->loopAnswers(self::ANSWER_Q_KEY . $key, $question->{ANSWER_KEY});
        }
    }

    private function loopAnswers(string $answerKey, array $answers): void {
        foreach ($answers as $answer) {
            if (isset($this->objectAnswers->{$answerKey}) && (int) $answer->value === (int) $this->objectAnswers->{$answerKey}) {
                $answer->selected = true;
            } else {
                $answer->selected = false;
            }
        }
    }
}

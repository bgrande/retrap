<?php

declare(strict_types=1);

require_once __DIR__ . '/Extractable.php';

final class SatisfactionExtractor implements Extractable {
    private const NAME = 'Satisfaction';
    private const FALLBACK_QUESTION_TEXT = 'Were these questions helpful?';

    private int $satisfactionQuestionId;

    public function __construct(int $questionId) {
        $this->satisfactionQuestionId = $questionId;
    }

    public function extract(array $objects): \stdClass {
        $resultObject = new \stdClass();
        $resultObject->values = [];
        $resultObject->name = self::NAME;

        /** @var \stdClass $object */
        foreach ($objects as $object) {
            if (!$object || !property_exists($object, 'questions') || !$object->questions) {
                continue;
            }

            $resultObject->values[] = $this->getSatisfactionQuestion($object->questions);
        }


        return $resultObject;
    }

    private function getSatisfactionQuestion(array $questions): int {
        foreach ($questions as $question) {
            if (
                !property_exists($question, 'question') ||
                !$question->question
            ) {
                continue;
            }

            if ($this->isSatisfactionQuestion($question)) {
                foreach ($question->answers as $answer) {
                    if ($answer->selected === true) {
                        return $answer->value;
                    }
                }
            }
        }

        return 0;
    }

    private function isSatisfactionQuestion(\stdClass $question): bool {
        if (
            !property_exists($question, 'question_id') ||
            !$question->question_id
        ) {
            return $question->question === self::FALLBACK_QUESTION_TEXT;
        }

        return $question->question_id === $this->satisfactionQuestionId;
    }
}

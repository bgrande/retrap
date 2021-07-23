<?php

declare(strict_types=1);

require_once __DIR__ . '/Extractable.php';

final class QuestionExtractor implements Extractable {
    private const NAME = 'Question';

    private $useIdFallback;

    public function __construct(bool $useIdFallback = false) {
        $this->useIdFallback = $useIdFallback;
    }

    public function extract(array $objects): \stdClass {
        $byQuestionList = [];

        $collection = new \stdClass();
        $collection->set = [];

        /**
         * iterating over the files (json as object)
         * @var \stdClass $object
         */
        foreach ($objects as $object) {
            if (!$object || !property_exists($object, 'questions') || !$object->questions) {
                continue;
            }

            $byQuestionList = $this->getPerQuestion($object->questions, $byQuestionList);

            $byQuestionList = $this->addValuesPerQuestion($object->questions, $byQuestionList);
        }

        $collection->set = $byQuestionList;

        return $collection;
    }

    private function getPerQuestion(array $questions, array $byQuestionList): array {
        foreach ($questions as $question) {
            if ($this->isQuestionAlreadyInList($question, $byQuestionList)) {
                return $byQuestionList;
            }

            if (
                !property_exists($question, 'answers') ||
                !$question->answers
            ) {
                continue;
            }

            $id = $this->getQuestionHash($question);

            $resultObject = new \stdClass();
            $resultObject->values = [];
            $resultObject->labels = [];
            $resultObject->name = self::NAME . ': ' . $question->question;

            $byQuestionList[$id] = $resultObject;
        }

        return $byQuestionList;
    }

    private function isQuestionAlreadyInList(\stdClass $question, array $list): bool {
        return isset($list[$this->getQuestionHash($question)]);
    }

    private function getQuestionHash(\stdClass $question): string {
        if (property_exists($question, 'question_id') && !$this->useIdFallback) {
            return md5((string) $question->question_id);
        }

        if (property_exists($question, 'question')) {
            return md5($question->question);
        }

        throw new \RuntimeException('something went wrong accessing the question response!');
    }

    private function addValuesPerQuestion(array $questions, array $byQuestionList): array {
        foreach ($questions as $question) {
            if (
                !property_exists($question, 'answers') || !$question->answers
            ) {
                continue;
            }

            $id = $this->getQuestionHash($question);

            foreach ($question->answers as $answer) {
                if (
                    !$answer ||
                    !property_exists($answer, 'value') ||
                    !$answer->value === null ||
                    !property_exists($answer, 'text') ||
                    !$answer->text ||
                    !property_exists($answer, 'selected') ||
                    !isset($byQuestionList[$id])
                ) {
                    continue;
                }

                if (!isset($byQuestionList[$id]->values[$answer->value])) {
                    $byQuestionList[$id]->values[$answer->value] = 0;
                }

                if ($answer->selected === true) {
                    $byQuestionList[$id]->values[$answer->value]++;
                }

                if (!$byQuestionList[$id]->labels[$answer->value]) {
                    $byQuestionList[$id]->labels[$answer->value] = $answer->text;
                }
            }
        }

        return $byQuestionList;
    }
}

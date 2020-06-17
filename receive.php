<?php

declare(strict_types = 1);

ini_set('memory_limit', '5M');
ini_set('max_execution_time', '30');

const FROM_KEY = 'from';
const ANSWER_KEY = 'answers';
const CALC_KEY = 'calculated';
const ANSWER_TEXT_KEY = 'answer';
const ANSWER_RESULT_KEY = 'result';
const QUESTION_KEY = 'questions';

const BASE_DATA_PATH   = __DIR__ . '/data/';
const QUESTION_FILE    = BASE_DATA_PATH . 'q/en/questions.json';
const RESULT_FILE_PATH = BASE_DATA_PATH . 'r/';

$config = require __DIR__ . '/data/config.php';

require_once __DIR__ . '/src/Question/AnswerUpdater.php';
require_once __DIR__ . '/src/Result/Entity/Result.php';
require_once __DIR__ . '/src/Result/NoDataException.php';
require_once __DIR__ . '/src/File/EncodeName.php';
require_once __DIR__ . '/src/Mail/Send.php';
require_once __DIR__ . '/src/Uri/UriCreator.php';

$entityBody = file_get_contents('php://input');
$result = new stdClass();

$answerText = '';
$answerResult = '';
$textFrom = '';
$objectAnswers = null;
$questions = null;

if ($entityBody) {
    $objectified = json_decode($entityBody, false);

    if (isset($objectified->{ANSWER_KEY})) {
        $objectAnswers = $objectified->{ANSWER_KEY};

        if (is_string($objectAnswers)) {
            $objectAnswers = json_decode($objectAnswers, false);
        }
    }

    if (isset($objectified->{CALC_KEY})) {
        $objectCalc = $objectified->{CALC_KEY};

        if (is_string($objectCalc)) {
            $objectCalc = json_decode($objectCalc, false);
        }

        if ($objectCalc->{ANSWER_TEXT_KEY}) {
            $answerText = $objectCalc->{ANSWER_TEXT_KEY};
        }

        if ($objectCalc->{ANSWER_RESULT_KEY}) {
            $answerResult = (int) $objectCalc->{ANSWER_RESULT_KEY};
        }
    }

    if (isset($objectified->{FROM_KEY})) {
        $textFrom = $objectified->{FROM_KEY};
    }
}

if (file_exists(QUESTION_FILE)) {
    $questions = json_decode(file_get_contents(QUESTION_FILE), false);
    $answerUpdater = new AnswerUpdater($questions, $objectAnswers);
    $answerUpdater->update();
}

header('Content-Type: application/json');

try {
    $writeResult = new Result($questions->{QUESTION_KEY}, $answerText, (int) $questions->v, $answerResult, $questions->lang, $textFrom);
    $fileNameEncoder = new EncodeName();
    $fileName = $fileNameEncoder->encode(RESULT_FILE_PATH);

    if ($fileName === '') {
        error_log('could not find a valid filename');
        echo 0;
    }

    $extensionName = $fileName . EncodeName::FILE_EXTENSION;

    file_put_contents(RESULT_FILE_PATH . $extensionName, json_encode($writeResult, JSON_PRETTY_PRINT));

    $uriCreator = new UriCreator($_SERVER);
    $uri = $uriCreator->createUri('/data/r/' . $extensionName);

    $mailer = new Send($config);
    $mailer->compose('New Recruiting request from ReTrap', "You've got another request: \n" . $uri);
} catch (NoDataException $exception) {
    error_log($exception->getMessage());
    echo 0;
}

echo 1;

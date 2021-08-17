<?php

declare(strict_types = 1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/src/Question/AnswerUpdater.php';
require_once __DIR__ . '/src/Result/Entity/Result.php';
require_once __DIR__ . '/src/Result/NoDataException.php';
require_once __DIR__ . '/src/File/EncodeName.php';
require_once __DIR__ . '/src/Mail/Send.php';
require_once __DIR__ . '/src/Uri/UriCreator.php';
require_once __DIR__ . '/src/Anonymize/Anonymizer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'No data received!';
    exit;
}

$entityBody = file_get_contents('php://input');
$result = new stdClass();

$answerText = '';
$answerResult = 0;
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
            $answerText = htmlentities(strip_tags($objectCalc->{ANSWER_TEXT_KEY}));
        }

        if ($objectCalc->{ANSWER_RESULT_KEY} || $objectCalc->{ANSWER_RESULT_KEY} === 0) {
            $answerResult = (int) $objectCalc->{ANSWER_RESULT_KEY};
        }
    }

    if (isset($objectified->{FROM_KEY})) {
        $textFrom = htmlentities(strip_tags($objectified->{FROM_KEY}));
    }
}

if (file_exists(QUESTION_FILE)) {
    $questions = json_decode(file_get_contents(QUESTION_FILE), false);
    $answerUpdater = new AnswerUpdater($questions, $objectAnswers);
    $answerUpdater->update();
}

header('Content-Type: application/json');

try {
    $anonymize = new Anonymizer();
    $fromId = $anonymize->anonymize($_SERVER['REMOTE_ADDR']);

    $writeResult = new Result($questions->{QUESTION_KEY}, $answerText, (int) $questions->v, $answerResult, $questions->lang, $textFrom, $fromId);
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

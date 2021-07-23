<?php

declare(strict_types=1);

ini_set('error_reporting', 'true');

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/src/File/FileGetter.php';
require_once __DIR__ . '/src/Analytic/Format/PerMonthFormatter.php';
require_once __DIR__ . '/src/Analytic/Format/SatisfactionFormatter.php';
require_once __DIR__ . '/src/Analytic/Format/QuestionFormatter.php';
require_once __DIR__ . '/src/Analytic/Analyzer.php';
require_once __DIR__ . '/src/Analytic/Extract/PerMonthExtractor.php';
require_once __DIR__ . '/src/Analytic/Extract/SatisfactionExtractor.php';
require_once __DIR__ . '/src/Analytic/Extract/QuestionExtractor.php';

function errorResponse(string $missing) {
    echo <<<RES
{
    "error": "no ${missing} set!"
}
RES;
    exit;
}

$type = $_GET['type'] ?? null;
$questionId = $_GET['question_id'] ?? null;

header('Content-Type: application/json');

$getter = new FileGetter();

if (!$type) {
    errorResponse('type');
}

switch ($type) {
    case 'month':
    case 'time':
        $formatter = new PerMonthFormatter();
        $extractor = new PerMonthExtractor();
        break;
    case 'dayofweek':
        // @todo requests per day of week
        break;
    case 'satisfaction':
        if (!$questionId) {
            errorResponse('question_id');
        }

        $formatter = new SatisfactionFormatter();
        $extractor = new SatisfactionExtractor((int) $questionId);
        break;
    case 'questions':
        $formatter = new QuestionFormatter();
        $extractor = new QuestionExtractor(true);
        break;
}

$perMonth = new Analyzer($getter, $formatter, $extractor, RESULT_FILE_PATH);

echo $perMonth->compute();

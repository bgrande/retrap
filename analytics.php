<?php

declare(strict_types=1);

ini_set('error_reporting', 'true');

require_once __DIR__ . '/boostrap.php';
require_once __DIR__ . '/src/File/FileGetter.php';
require_once __DIR__ . '/src/Analytic/Format/PerMonthFormatter.php';
require_once __DIR__ . '/src/Analytic/Analyzer.php';
require_once __DIR__ . '/src/Analytic/Extract/PerMonthExtractor.php';

$type = null;

if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

header('Content-Type: application/json');

$getter = new FileGetter();

if (!$type) {
    echo <<<RES
{
    'error': 'no type set!'
}
RES;
    exit;
}

switch ($type) {
    case 'month':
        $formatter = new PerMonthFormatter();
        $extractor = new PerMonthExtractor();
        break;
    case 'dayofweek':
        // @todo requests per day of week
        break;
    case 'satisfaction':
        // @todo requests with satisfaction levels
        break;
    case 'question_X':
        // @todo request answers for different question
        break;
}

$perMonth = new Analyzer($getter, $formatter, $extractor, RESULT_FILE_PATH);

echo $perMonth->compute();

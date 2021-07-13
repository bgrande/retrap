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

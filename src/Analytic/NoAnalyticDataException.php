<?php

declare(strict_types = 1);

final class NoAnalyticDataException extends \RuntimeException {
    public function __construct() {
        parent::__construct('Value or name not set for Analytic data');
    }
}

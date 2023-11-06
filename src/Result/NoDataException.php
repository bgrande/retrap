<?php

declare(strict_types = 1);

final class NoDataException extends \RuntimeException {
    public function __construct() {
        parent::__construct('Required data do not exist');
    }
}

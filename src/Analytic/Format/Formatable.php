<?php

declare(strict_types=1);

interface Formatable {
    /**
     * @return string json encoded string to be consumed by diagram
     */
    public function format(\stdClass $toFormat): string;
}

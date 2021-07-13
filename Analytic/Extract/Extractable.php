<?php

declare(strict_types=1);

interface Extractable {
    /**
     *  extract needed data from response file for specific analytic case
     * @return array
     */
    public function extract(array $objects): \stdClass;
}

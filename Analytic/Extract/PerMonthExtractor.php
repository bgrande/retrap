<?php

declare(strict_types=1);

require_once __DIR__ . '/Extractable.php';

final class PerMonthExtractor implements Extractable {
    private const NAME = 'Monthly';

    public function extract(array $objects): \stdClass {
        $resultObject = new \stdClass();
        $resultObject->values = [];
        $resultObject->name = self::NAME;

        /** @var \stdClass $object */
        foreach ($objects as $object) {
            if (!$object->date) {
                continue;
            }

            $resultObject->values[] = $object->date;
        }


        return $resultObject;
    }
}

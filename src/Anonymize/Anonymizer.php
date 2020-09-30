<?php
declare(strict_types=1);

final class Anonymizer {
	/** @var string */
	private const CHAR_SEED = 'abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	/** @var int */
	private const SHA_LENGTH = 40;
	private const REPLACEMENTS = 3;

	/**
	 * We're anonymizing based on the original sha1 value.
	 * So we can still create the same hash for the same IP address
	 * but it's more difficult to reproduce the original IP
	 * (you can only do that with running this script on a known set of IP addresses).
	 *
	 * @param string $value
	 * @return string
	 */
	public function anonymize(string $value): string {
		$useRandom = $this->getRandoms($value);

		$replacements = '';
		foreach ($useRandom as $random) {
			$replacements .= self::CHAR_SEED[$random];
		}

		$newValue = substr_replace($value, $replacements, self::SHA_LENGTH, -self::REPLACEMENTS);

		return sha1($newValue);
	}

	/**
	 * @param string $value
	 * @return array
	 */
	private function getRandoms(string $value): array {
		$sha1Original = sha1($value);
		$useRandom = [];

		for ($i = 0, $l = self::SHA_LENGTH; $i < $l; $i ++) {
			$current = $sha1Original[$i];
			$currentInt = (int) $sha1Original[$i];

			// make sure we don't have a casted string here
			if (\is_int($currentInt) && $current === (string) $currentInt) {
				$useRandom[] = $i;
			}

			if (\count($useRandom) === self::REPLACEMENTS) {
				break;
			}
		}

		return $useRandom;
	}
}

<?php

declare(strict_types = 1);

class Send {
    /** @var array */
    private $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function compose(string $subject, string $text): void {
        if (isset($this->config['send_mail'], $this->config['email_address']) && $this->config['send_mail'] === true && $this->config['email_address'] !== '') {
            $success = mail($this->config['email_address'], $subject, $text);

            if (!$success) {
              error_log('sending mail failed');
            }
        }
    }
}

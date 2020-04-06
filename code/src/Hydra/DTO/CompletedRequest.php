<?php

namespace App\Hydra\DTO;

use InvalidArgumentException;

class CompletedRequest {

    private $redirect_to;

    private function __construct(array $data) {
        $important_keys = [
          'redirect_to',
        ];

        $missing_keys = array_diff($important_keys, array_keys($data));

        if (!empty($missing_keys)) {
            throw new InvalidArgumentException(sprintf('Missing many important array items: %s', implode(', ', $missing_keys)));
        }

        $this->redirect_to = (string)$data['redirect_to'];
    }

    public static function fromArray(array $data): self {
        return new CompletedRequest($data);
    }

    final public function getRedirectTo(): string {
        return $this->redirect_to;
    }
}


<?php

namespace App\Service;

interface HydraClientInterface {
    public function fetchLogin(string $challenge);
    public function acceptLogin(string $challenge);
    public function rejectLogin(string $challenge);
}
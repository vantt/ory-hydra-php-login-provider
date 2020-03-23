<?php

namespace App\Service;

interface HydraClientInterface {
    public function fetchLoginRequest(string $challenge);
    public function acceptLogin(string $challenge);
    public function rejectLogin(string $challenge);
}
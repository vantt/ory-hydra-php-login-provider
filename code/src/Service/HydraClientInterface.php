<?php

namespace App\Service;

interface HydraClientInterface {
    public function fetchLogin($challenge);
    public function acceptLogin($challenge);
    public function rejectLogin($challenge);
}
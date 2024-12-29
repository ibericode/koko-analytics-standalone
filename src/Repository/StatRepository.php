<?php

namespace App\Repository;

use App\Database;

class StatRepository {
    public function __construct(
        protected Database $db
    ) {}
}

<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class CaseStatus
{
    const OPEN = 'open';
    const CLOSED = 'closed';

    public static $dict = [
        self::OPEN => "Open",
        self::CLOSED => "Closed",
    ];
}
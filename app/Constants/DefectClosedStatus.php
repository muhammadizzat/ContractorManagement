<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class DefectClosedStatus
{
    const DUPLICATE = 'duplicate';
    const REJECTED = 'rejected';

    public static $dict = [
        self::DUPLICATE => "duplicate",
        self::REJECTED => "rejected",
    ];
}
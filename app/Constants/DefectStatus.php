<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class DefectStatus
{
    const OPEN = 'open';
    const WIP = 'wip';
    const RESOLVED = 'resolved';
    const CLOSED = 'closed';

    public static $dict = [
        self::OPEN => "Open",
        self::WIP => "Work In Progress",
        self::RESOLVED => "Resolved",
        self::CLOSED => "Closed",
    ];
}
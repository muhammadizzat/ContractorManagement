<?php

namespace App\Services;

use DB;
use Auth;
use Mail;

use App\ProjectCase;
use App\Defect;


class RunningNumberService
{
    public static function nextCaseRefNo($developer_id)
    {
        $latestCaseRefNo = ProjectCase::withTrashed()->where('developer_id', $developer_id)->max('ref_no');
        if(!empty($latestCaseRefNo)) {
            return $latestCaseRefNo + 1;
        } else {
            return 1;
        }
    }

    public static function nextCaseDefectNo($developer_id)
    {
        $latestDefectRefNo = Defect::withTrashed()->where('developer_id', $developer_id)->max('ref_no');
        if(!empty($latestDefectRefNo)) {
            return $latestDefectRefNo + 1;
        } else {
            return 1;
        }
    }
}

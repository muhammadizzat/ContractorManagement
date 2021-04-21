<?php

namespace App\Http\Controllers\Developer\Admin;

use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Constants\CaseStatus;
use App\Constants\DefectStatus;


use App\Defect;
use App\DefectType;
use App\Unit;
use App\CaseTag;
use App\DefectTag;
use App\ProjectCase;

class ProjectDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:dev-admin']);
        $this->middleware('project.dev-admin.access');
    }

    public function index($proj_id)
    {
        return view('dev-admin.projects.dashboard', ['proj_id' => $proj_id]);
    }

    public function ajaxGetByTagsStats($proj_id)
    {
        $caseTags = CaseTag::whereHas('case', function ($query) use ($proj_id){
            $query->where('project_id', $proj_id);
        })
        ->groupBy('tag')
        ->select(DB::raw('tag, count(*) as count'))
        ->get();

        $defectTags = DefectTag::whereHas('defect', function ($query) use ($proj_id){
            $query->where('project_id', $proj_id);
        })
        ->groupBy('tag')
        ->select(DB::raw('tag, count(*) as count'))
        ->get();
        
        $caseStats = [];
        $defectStats = [];

        foreach($caseTags as $caseTag) {
            $case_tag = $caseTag->tag;
            $case_tag_count = $caseTag->count;

            array_push($caseStats, [
                'case_tag' => $case_tag,
                'case_tag_count' => $case_tag_count,
            ]);
        }
        
        foreach($defectTags as $defectTag) {
            $defect_tag = $defectTag->tag;
            $defect_tag_count = $defectTag->count;

            array_push($defectStats, [
                'defect_tag' => $defect_tag,
                'defect_tag_count' => $defect_tag_count,
            ]);
        }

        return response()->json([
            'data' => [
                'cases_tags_data' => $caseStats,
                'defects_tags_data' => $defectStats,
            ]
        ]);
    }

    public function ajaxGetByDefectTypeStats($proj_id)
    {
        $defectTypes = DefectType::whereHas('defects', function ($query) use ($proj_id) {
            $query->where('project_id', $proj_id);
        })
            ->select('id', 'title')
            ->get();
        $stats = [];
        foreach ($defectTypes as $defectType) {
            $defects_stats = Defect::where('project_id', $proj_id)
                ->where('defect_type_id', $defectType->id)
                ->groupBy('status')
                ->select(DB::raw('status, count(*) as count'))
                ->get();

            array_push($stats, [
                'defect_stats' => $defects_stats,
                'defect_type' => $defectType,
            ]);
        }

        return response()->json([
            'related_data' => [
                'defect_statuses' => DefectStatus::$dict
            ],
            'data' => $stats
        ]);
    }

    private $dateRange = [
        [
            'from' => 6,
            'to' => 0
        ],
        [
            'from' => 14,
            'to' => 7
        ],
        [
            'from' => 22,
            'to' => 15
        ],
        [
            'from' => 30,
            'to' => 23
        ],
        [
            'from' => 59,
            'to' => 31
        ],
        [
            'from' => 89,
            'to' => 60
        ],
        [
            'from' => 119,
            'to' => 90
        ],
        [
            'to' => 120
        ]

    ];

    public function ajaxGetByDefectsStats($proj_id)
    {

        $defects_stats = Defect::where('project_id', $proj_id)
            ->groupBy('status')
            ->select(DB::raw('status, count(*) as count'))
            ->get();

        $defects_tracking_stats = [];
        $now = Carbon::now();
        foreach ($this->dateRange as $stats_options) {
            $q = Defect::forProject($proj_id)->unclosed()->whereDate('due_date', '>', $now);

            $stats_entry = $stats_options;

            if (!empty($stats_options['from'])) {
                $fromDate = $now->copy()->subDay($stats_options['from']);
                $q = $q->whereDate('created_at', '>', $fromDate);
            }

            if (!empty($stats_options['to'])) {
                $toDate = $now->copy()->subDay($stats_options['to']);
                $q = $q->whereDate('created_at', '<', $toDate);
            }

            $stats_entry['count'] = $q->count();

            $defects_tracking_stats[] = $stats_entry;
        }

        $overdueCount = Defect::forProject($proj_id)->unclosed()->whereDate("due_date", '<', $now)->count();

        return [
            "related_data" => [
                "defect_statuses" => DefectStatus::$dict
            ],
            "data" => [
                'defect_stats' => $defects_stats,
                'defects_tracking' => $defects_tracking_stats,
                'defectsOverdue' => $overdueCount,
            ]
        ];
    }

    const DEFECTS_OPEN_TO_CLOSED_WITHIN_DAYS_ENTRIES = [
        15
    ];

    const CASES_CLOSED_WITHIN_DAYS_ENTRIES = [
        30
    ];

    const DEFECTS_AVG_DAYS_STATUS_ENTRIES = [
        [
            "from_status" => DefectStatus::OPEN,
            "to_status" => DefectStatus::RESOLVED,
        ],
        [
            "from_status" => DefectStatus::RESOLVED,
            "to_status" => DefectStatus::CLOSED,
        ],
    ];

    const CASES_AVG_DAYS_STATUS_ENTRIES = [
        [
            "from_status" => CaseStatus::OPEN,
            "to_status" => CaseStatus::CLOSED,
        ],
    ];

    public function ajaxDefectGetByResponseTimesStats($proj_id)
    {
        $cases = ProjectCase::forProject($proj_id);
        $defects = Defect::forProject($proj_id);

        $defects_open_to_closed_within_days_stats = [];
        foreach(self::DEFECTS_OPEN_TO_CLOSED_WITHIN_DAYS_ENTRIES as $days) {

            $defects_open_to_closed_within_days_stats[] = [
                "days" => $days,
                "count" => (clone $defects)->whereRaw('DATEDIFF(closed_date,created_at) < '.$days)
                        // ->setBindings([$days])
                        ->count()
            ];
        }

        $cases_closed_within_days_stats = [];
        foreach(self::CASES_CLOSED_WITHIN_DAYS_ENTRIES as $days) {

            $cases_closed_within_days_stats[] = [
                "days" => $days,
                "count" => (clone $cases)->whereRaw('DATEDIFF(closed_date,created_at) < '.$days)
                        ->count()
            ];
        }

        $defects_avg_days_stats = [];
        foreach(self::DEFECTS_AVG_DAYS_STATUS_ENTRIES as $option) {
            $stat_entry = [];

            $from_date_column;
            switch($option['from_status']) {
                case DefectStatus::OPEN:
                    $from_date_column = "created_at";
                    break;
                case DefectStatus::RESOLVED:
                    $from_date_column = "resolved_date";
                    break;
                case DefectStatus::CLOSED:
                    $from_date_column = "closed_date";
                    break;
            }
            $to_date_column;
            switch($option['to_status']) {
                case DefectStatus::OPEN:
                    $to_date_column = "created_at";
                    break;
                case DefectStatus::RESOLVED:
                    $to_date_column = "resolved_date";
                    break;
                case DefectStatus::CLOSED:
                    $to_date_column = "closed_date";
                    break;
            }

            $avg_days = (clone $defects)->select(\DB::raw("DATEDIFF($to_date_column, $from_date_column) AS day_diff"))->get()->avg('day_diff');

            $defects_avg_days_stats[] = [
               "from_status" => DefectStatus::$dict[$option['from_status']],
               "to_status" => DefectStatus::$dict[$option['to_status']],
               "avg_days" => $avg_days,
            ];
        }

        $cases_avg_days_stats = [];
        foreach(self::CASES_AVG_DAYS_STATUS_ENTRIES as $option) {
            $stat_entry = [];

            $from_date_column;
            switch($option['from_status']) {
                case CaseStatus::OPEN:
                    $from_date_column = "created_at";
                    break;
                case CaseStatus::CLOSED:
                    $from_date_column = "closed_date";
                    break;
            }
            $to_date_column;
            switch($option['to_status']) {
                case CaseStatus::OPEN:
                    $to_date_column = "created_at";
                    break;
                case CaseStatus::CLOSED:
                    $to_date_column = "closed_date";
                    break;
            }

            $avg_days = (clone $cases)->select(\DB::raw("DATEDIFF($to_date_column, $from_date_column) AS day_diff"))->get()->avg('day_diff');

            $cases_avg_days_stats[] = [
               "from_status" => CaseStatus::$dict[$option['from_status']],
               "to_status" => CaseStatus::$dict[$option['to_status']],
               "avg_days" => $avg_days,
            ];
        }

        return [
            "data" => [
                "total_defects" => (clone $defects)->count(),
                "total_cases" => (clone $cases)->count(),
                "defects_open_to_closed_within_days" => $defects_open_to_closed_within_days_stats,
                "cases_closed_within_days" => $cases_closed_within_days_stats,
                "defects_avg_days" => $defects_avg_days_stats,
                "cases_avg_days" => $cases_avg_days_stats,
            ]
        ];
    }

    public function ajaxGetByUnitsStats($proj_id)
    {
        $totalUnitsByProject = Unit::where('project_id', $proj_id)->count();
        $totalUnitsWithDefectsByProject = Unit::where('project_id', $proj_id)

            ->whereHas('cases', function ($q) {
                $q->whereHas('defects', function ($q) {
                    $q->unclosed();
                });
            })->count();

        return response()->json([
            'total_unit' => $totalUnitsByProject,
            'total_unit_with_defects' => $totalUnitsWithDefectsByProject,
        ]);
    }
}

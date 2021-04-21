<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Api\UserController@login');
Route::post('login/contractor', 'Api\UserController@contractorLogin');
Route::post('login/cow', 'Api\UserController@clerkOfWorkLogin');

Route::post('reset-user-password/{user_id}', 'Api\ResetPasswordController@resetUserPassword')->where(['user_id' => '[0-9]+']);
// Route::get('user/info', 'Api\UserController@info');
Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function() {
    Route::get('info', 'Api\UserController@info');

    Route::get('notifications', 'Api\NotificationController@getNotifications');
    Route::get('notifications/stats', 'Api\NotificationController@getNotificationsStats');
    Route::post('notifications/mark-all-as-read', 'Api\NotificationController@markAllNotificationsAsRead');
    
    Route::get('profile', 'Api\ProfileController@getProfile');
    Route::post('profile', 'Api\ProfileController@postUpdateProfile');
    Route::get('profile/pic', 'Api\ProfileController@getProfilePic');
    Route::post('profile/pic', 'Api\ProfileController@postProfilePic');
    Route::post('profile/password/edit','Api\ProfileController@postUserChangePassword');
});


Route::group(['prefix' => 'dev-cow', 'middleware' => ['auth:api', 'role:cow']], function() {
    Route::get('constants/case-statuses', 'Api\Developer\ClerkOfWork\ConstantController@getCaseStatuses');
    Route::get('constants/defect-statuses', 'Api\Developer\ClerkOfWork\ConstantController@getDefectStatuses');
    
    Route::get('clerks-of-work', 'Api\Developer\ClerkOfWork\ClerkOfWorkController@getClerkOfWorks');
    Route::post('contractors/search', 'Api\Developer\ClerkOfWork\ContractorController@postSearchContractors');
    
    Route::get('projects', 'Api\Developer\ClerkOfWork\ProjectController@get');
    Route::post('projects/{proj_id}/calendar/data', 'Api\Developer\ClerkOfWork\ProjectCalendarController@postGetCalendarData');
    Route::get('projects/{proj_id}/clerks-of-work', 'Api\Developer\ClerkOfWork\ProjectController@getProjectClerkOfWorkUsers');
    Route::get('projects/{proj_id}/cases', 'Api\Developer\ClerkOfWork\CaseController@getProjectCases')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases', 'Api\Developer\ClerkOfWork\CaseController@postAddCase')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}', 'Api\Developer\ClerkOfWork\CaseController@getProjectCase')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/assign-cow', 'Api\Developer\ClerkOfWork\CaseController@postAssignCow')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/status', 'Api\Developer\ClerkOfWork\CaseController@postCaseStatus')->name('dev-admin.projects.cases.ajax.status.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/description', 'Api\Developer\ClerkOfWork\CaseController@postCaseDescription')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/tags', 'Api\Developer\ClerkOfWork\CaseController@postCaseTags')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    
    Route::post('projects/{proj_id}/cases/{case_id}/defects', 'Api\Developer\ClerkOfWork\DefectController@postDefect')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}', 'Api\Developer\ClerkOfWork\DefectController@getDefect')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}', 'Api\Developer\ClerkOfWork\DefectController@getDefectImage')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/images', 'Api\Developer\ClerkOfWork\DefectController@postDefectImage')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}/delete', 'Api\Developer\ClerkOfWork\DefectController@postDeleteDefectImage')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/pins', 'Api\Developer\ClerkOfWork\DefectController@postPins')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/assign-contractor', 'Api\Developer\ClerkOfWork\DefectController@postDefectContractor')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/status', 'Api\Developer\ClerkOfWork\DefectController@postDefectStatus')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/description', 'Api\Developer\ClerkOfWork\DefectController@postDescription')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/tags', 'Api\Developer\ClerkOfWork\DefectController@postDefectTags')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/requests/pending-request', 'Api\Developer\ClerkOfWork\DefectController@getPendingRequest')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/requests/{activity_id}/response', 'Api\Developer\ClerkOfWork\DefectController@postRequestResponse')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+']);

    Route::get('projects/{proj_id}/defects', 'Api\Developer\ClerkOfWork\DefectController@getAllDefects')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/defects/search', 'Api\Developer\ClerkOfWork\DefectController@postSearchDefects')->where(['proj_id' => '[0-9]+']);


    // SECTION: Defect Activity
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/activities', 'Api\Developer\ClerkOfWork\DefectController@getDefectActivities')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/activities/comment', 'Api\Developer\ClerkOfWork\DefectController@postAddActivityComment')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/activities/{activity_id}/images/{id}', 'Api\Developer\ClerkOfWork\DefectController@getDefectActivityImage')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);

    Route::get('projects/{proj_id}/units', 'Api\Developer\ClerkOfWork\UnitController@getUnits')->where(['proj_id' => '[0-9]+']);
    
    // Route::get('projects/{proj_id}/units/{unit_id}', 'Api\Developer\ClerkOfWork\UnitController@getUnit')->where(['proj_id' => '[0-9]+', 'unit_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}', 'Api\Developer\ClerkOfWork\UnitTypeController@getUnitType')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}/floor/{floor_id}/floor-plan', 'Api\Developer\ClerkOfWork\UnitTypeController@getUnitTypeFloorPlan')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+', 'floor_id' => '[0-9]+']);
    
    Route::get('defect-types', 'Api\Developer\ClerkOfWork\DefectTypeController@getDefectTypes');

    // Section: Dashboard    
    Route::get('dashboard/stats', 'Api\Developer\ClerkOfWork\DashboardController@getStats');
});

// API: Contractor
Route::group(['prefix' => 'contractor', 'middleware' => ['auth:api', 'role:contractor']], function () {
    Route::get('constants/defect-statuses', 'Api\Contractor\ConstantController@getDefectStatuses');

    Route::get('defects/me/summary', 'Api\Contractor\DefectController@getDefectsSummary');
    Route::get('defects/me', 'Api\Contractor\DefectController@getDefects');

    Route::get('defects/{id}', 'Api\Contractor\DefectController@getDefectInfo')->where(['id' => '[0-9]+']);
    Route::get('defects/{id}/activities', 'Api\Contractor\DefectController@getDefectActivities')->where(['id' => '[0-9]+']);
    Route::get('defects/{defect_id}/images/{id}', 'Api\Contractor\DefectController@getDefectImage')->where(['defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('defects/{defect_id}/activities/{activity_id}/images/{id}', 'Api\Contractor\DefectController@getDefectActivityImage')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('defects/{id}/activities/comment', 'Api\Contractor\DefectController@postAddActivityComment')->where(['id' => '[0-9]+']);
    Route::post('defects/{id}/status', 'Api\Contractor\DefectController@postDefectStatus')->where(['id' => '[0-9]+']);
    Route::post('defects/{id}/request', 'Api\Contractor\DefectController@postDefectRequest')->where(['id' => '[0-9]+']);
    Route::get('defects/{id}/requests/pending-request', 'Api\Contractor\DefectController@getPendingRequest')->where(['id' => '[0-9]+']);
    Route::post('defects/{defect_id}/request/{activity_id}/cancel', 'Api\Contractor\DefectController@postDefectRequestCancel')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+']);

    // TODO Improve access
    Route::get('projects/{proj_id}/units/{unit_id}/type', 'Api\Contractor\UnitController@getUnitUnitType')->where(['proj_id' => '[0-9]+', 'unit_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/{unit_id}/floors/{floor_id}/floor-plan', 'Api\Contractor\UnitController@getUnitFloorPlan')->where(['proj_id' => '[0-9]+', 'unit_id' => '[0-9]+', 'floor_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}/floor/{floor_id}/floor-plan', 'Api\Contractor\UnitTypeController@getUnitTypeFloorPlanImage')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+', 'floor_id' => '[0-9]+']);

    // Section: Dashboard
    Route::get('dashboard/stats', 'Api\Contractor\DashboardController@getStats');
});

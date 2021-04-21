<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\Api\NotificationController;

Route::get('/', function () {
    if (\Auth::check()) {
        if (\Auth::user()->hasRole('super-admin')) {
            return redirect()->route("admin.dashboard");
        } else if (\Auth::user()->hasRole('admin')) {
            return redirect()->route("admin.dashboard");
        } else if (\Auth::user()->hasRole('cow')) {
            return redirect()->route("dev-cow.dashboard");
        } else if (\Auth::user()->hasRole('dev-admin')) {
            return redirect()->route("dev-admin.dashboard");
        } else if (\Auth::user()->hasRole('contractor')) {
            return redirect()->route("contractor.dashboard");
        }
    } else {
        return view('welcome');
    }
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::post('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::get('profile/{id}/image', 'ProfileController@getUserProfilePicture')->name('profile.users.image')->where(['id' => '[0-9]+']);
    Route::get('profile/password/edit','AuthController@userChangePassword')->name('profile.password');  
    Route::post('profile/password/edit','AuthController@postUserChangePassword')->name('profile.password.post');

    //Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    Route::get('notifications/index', 'NotificationController@index')->name('notifications.page');
    Route::get('notifications/dt', 'NotificationController@getDataTableNotifications')->name('notifications.dt');
    Route::get('notifications', 'NotificationController@ajaxGetNotifications')->name('notifications.get');
    Route::get('notifications/stats', 'NotificationController@ajaxGetNotificationsStats')->name('notifications.stats.get');
    Route::post('notifications/mark-all-as-read', 'NotificationController@ajaxMarkAllNotificationsAsRead')->name('notifications.mark-all-read.post');
});

// APP: Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin|super-admin']], function () {
    Route::get('', 'Admin\DashboardController@index')->name('admin.dashboard');
    Route::get('audit-log', 'Admin\AuditLogController@index')->name('admin.audit-log.index');
    
    // SECTION: Users
    Route::get('users', 'Admin\UserController@displayUsers')->name('admin.users.index');
    Route::get('users/dt', 'Admin\UserController@getDataTableUsers')->name('admin.users.dt');
    Route::get('users/{id}/profile', 'Admin\UserController@edit')->name('admin.users.edit')->where(['id' => '[0-9]+']);
    Route::post('users/{id}/profile', 'Admin\UserController@update')->name('admin.users.update')->where(['id' => '[0-9]+']);
    Route::post('users/{id}/delete', 'Admin\UserController@deleteUser')->name('admin.users.delete')->where(['id' => '[0-9]+']);

    // SECTION: Admins
    Route::get('admins', 'Admin\AdminController@displayAdmins')->name('admin.admins.index');
    Route::get('admins/dt', 'Admin\AdminController@getDataTableAdmin')->name('admin.admins.dt');
    Route::get('admins/export', 'Admin\AdminController@getAdminsExcelExport')->name('admin.admins.export');
    Route::get('admins/add', 'Admin\AdminController@addAdmin')->name('admin.admins.add');
    Route::post('admins/add', 'Admin\AdminController@postAddAdmin')->name('admin.admins.add.post');
    Route::get('admins/{id}/edit', 'Admin\AdminController@editAdmin')->name('admin.admins.edit')->where(['id' => '[0-9]+']);
    Route::post('admins/{id}/edit', 'Admin\AdminController@postEditAdmin')->name('admin.admins.edit.post')->where(['id' => '[0-9]+']);
    Route::post('admins/{id}/delete', 'Admin\AdminController@deleteAdmin')->name('admin.admins.delete')->where(['id' => '[0-9]+']);

    // SECTION: Developers
    Route::get('developers', 'Admin\DeveloperController@displayDevelopers')->name('admin.developers.index');
    Route::get('developers/dt', 'Admin\DeveloperController@getDataTableDeveloper')->name('admin.developers.dt');

    Route::get('developers/add', 'Admin\DeveloperController@addDeveloper')->name('admin.developers.add');
    Route::post('developers/add', 'Admin\DeveloperController@postAddDeveloper')->name('admin.developers.add.post');
    Route::get('developers/{dev_id}/edit', 'Admin\DeveloperController@editDeveloper')->name('admin.developers.edit')->where(['dev_id' => '[0-9]+']);
    Route::post('developers/{dev_id}/edit', 'Admin\DeveloperController@postEditDeveloper')->name('admin.developers.edit.post')->where(['dev_id' => '[0-9]+']);
    Route::post('developers/{dev_id}/delete', 'Admin\DeveloperController@deleteDeveloper')->name('admin.developers.delete')->where(['dev_id' => '[0-9]+']);
    Route::get('developers/{dev_id}/logo', 'Admin\DeveloperController@getDeveloperLogo')->name('admin.developers.logo')->where(['dev_id' => '[0-9]+']);

    Route::get('developer-admins', 'Admin\DeveloperAdminController@displayDevelopersAdmins')->name('admin.developer-admins.index');
    Route::get('developer-admins/dt', 'Admin\DeveloperAdminController@getDataTableDevelopersAdmins')->name('admin.developer-admins.dt');
    Route::get('developer-admins/export', 'Admin\DeveloperAdminController@getDevelopersAdminsExcelExport')->name('admin.developer-admins.export');

    Route::get('developer-admins/{id}/edit', 'Admin\DeveloperAdminController@editDevelopersAdmin')->name('admin.developer-admins.edit')->where(['id' => '[0-9]+']);
    Route::post('developer-admins/{id}/edit', 'Admin\DeveloperAdminController@postEditDevelopersAdmin')->name('admin.developer-admins.edit.post')->where(['id' => '[0-9]+']);
    Route::post('developer-admins/{id}/delete', 'Admin\DeveloperAdminController@deleteDevelopersAdmin')->name('admin.developer-admins.delete')->where(['id' => '[0-9]+']);


    Route::get('developers/{dev_id}/admins', 'Admin\DeveloperAdminController@displayDeveloperAdmins')->name('admin.developers.admins.index')->where(['dev_id' => '[0-9]+']);
    Route::get('developers/{dev_id}/admins/dt', 'Admin\DeveloperAdminController@getDataTableDeveloperAdmin')->name('admin.developers.admins.dt')->where(['dev_id' => '[0-9]+']);
    Route::get('developers/{dev_id}/admins/export', 'Admin\DeveloperAdminController@getDeveloperAdminsExcelExport')->name('admin.developers.admins.export')->where(['dev_id' => '[0-9]+']);;
    Route::get('developers/{dev_id}/admins/add', 'Admin\DeveloperAdminController@addDeveloperAdmin')->name('admin.developers.admins.add')->where(['dev_id' => '[0-9]+']);
    Route::post('developers/{dev_id}/admins/add', 'Admin\DeveloperAdminController@postAddDeveloperAdmin')->name('admin.developers.admins.add.post')->where(['dev_id' => '[0-9]+']);

    Route::get('developers/{dev_id}/admins/add', 'Admin\DeveloperAdminController@addDeveloperAdmin')->name('admin.developers.admins.add')->where('dev_id', '[0-9]+');
    Route::post('developers/{dev_id}/admins/add', 'Admin\DeveloperAdminController@postAddDeveloperAdmin')->name('admin.developers.admins.add.post')->where('dev_id', '[0-9]+');
    Route::get('developers/{dev_id}/admins/{id}/edit', 'Admin\DeveloperAdminController@editDeveloperAdmin')->name('admin.developers.admins.edit')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('developers/{dev_id?}/admins/{id}/edit', 'Admin\DeveloperAdminController@postEditDeveloperAdmin')->name('admin.developers.admins.edit.post')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('developers/{dev_id}/admins/{id}/delete', 'Admin\DeveloperAdminController@deleteDeveloperAdmin')->name('admin.developers.admins.delete')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);

    Route::get('developers/{dev_id}/projects', 'Admin\DeveloperProjectController@displayProjects')->name('admin.developers.projects.index')->where(['dev_id' => '[0-9]+']);
    Route::get('developers/{dev_id}/projects/{proj_id}/logo', 'Admin\DeveloperProjectController@getProjectLogo')->name('admin.developers.projects.logo')->where(['dev_id' => '[0-9]+', 'proj' => '[0-9]+']);
    Route::get('developers/{dev_id}/projects/dt', 'Admin\DeveloperProjectController@getDataTableProjects')->name('admin.developers.projects.dt')->where(['dev_id' => '[0-9]+']);

    Route::get('developers/{dev_id}/projects/add', 'Admin\DeveloperProjectController@addProject')->name('admin.developers.projects.add')->where(['dev_id' => '[0-9]+']);
    Route::post('developers/{dev_id}/projects/add', 'Admin\DeveloperProjectController@postAddProject')->name('admin.developers.projects.add.post')->where(['dev_id' => '[0-9]+']);

    // Route::get('developers/{dev_id}/projects/{id}/index', 'Admin\DeveloperProjectController@index')->name('admin.developers.projects.index')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('developers/{dev_id}/projects/{id}', 'Admin\DeveloperProjectController@viewProject')->name('admin.developers.projects.view')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('developers/{dev_id}/projects/{id}/edit', 'Admin\DeveloperProjectController@editProject')->name('admin.developers.projects.edit')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('developers/{dev_id}/projects/{id}/edit', 'Admin\DeveloperProjectController@postEditProject')->name('admin.developers.projects.edit.post')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('developers/{dev_id}/projects/{id}/delete', 'Admin\DeveloperProjectController@deleteDeveloperProject')->name('admin.developers.projects.delete')->where(['dev_id' => '[0-9]+', 'id' => '[0-9]+']);


    // SECTION: Contractors
    Route::get('contractors', 'Admin\ContractorController@displayContractor')->name('admin.contractors.index');
    Route::get('contractors/dt', 'Admin\ContractorController@getDataTableContractor')->name('admin.contractors.dt');
    Route::get('contractors/{id}/edit', 'Admin\ContractorController@editContractor')->name('admin.contractors.edit')->where(['id' => '[0-9]+']);
    Route::post('contractors/{id}/edit', 'Admin\ContractorController@postEditContractor')->name('admin.contractors.edit.post')->where(['id' => '[0-9]+']);
    Route::post('contractors/{id}/delete', 'Admin\ContractorController@deleteContractor')->name('admin.contractors.delete')->where(['id' => '[0-9]+']);

    // SECTION: Defect Types
    Route::get('configuration/defect-type', 'Admin\Configuration\DefectTypeController@displayDefectType')->name('admin.configuration.defect-types.index');
    Route::get('configuration/defect-type/dt', 'Admin\Configuration\DefectTypeController@getDataTableDefectType')->name('admin.configuration.defect-types.dt');
    Route::get('configuration/defect-type/add', 'Admin\Configuration\DefectTypeController@addDefectType')->name('admin.configuration.defect-types.add');
    Route::post('configuration/defect-type/add', 'Admin\Configuration\DefectTypeController@postAddDefectType')->name('admin.configuration.defect-types.add.post');
    Route::get('configuration/defect-type/{id}/edit', 'Admin\Configuration\DefectTypeController@editDefectType')->name('admin.configuration.defect-types.edit')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/edit', 'Admin\Configuration\DefectTypeController@postEditDefectType')->name('admin.configuration.defect-types.edit.post')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/delete', 'Admin\Configuration\DefectTypeController@deleteDefectType')->name('admin.configuration.defect-types.delete')->where(['id' => '[0-9]+']);
});

// APP: Developer Admin
Route::group(['prefix' => 'dev-admin', 'middleware' => ['auth', 'role:dev-admin']], function () {
    Route::get('', 'Developer\Admin\DashboardController@index')->name('dev-admin.dashboard');
    Route::get('audit-log', 'Developer\Admin\AuditLogController@index')->name('dev-admin.audit-log.index');

    // SECTION: Projects
    Route::get('projects', 'Developer\Admin\DeveloperProjectController@displayProjects')->name('dev-admin.projects.index');
    Route::get('projects/dt', 'Developer\Admin\DeveloperProjectController@getDataTableProjects')->name('dev-admin.projects.dt');
    Route::get('projects/{proj_id}/logo', 'Developer\Admin\DeveloperProjectController@getProjectLogo')->name('dev-admin.projects.logo')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/export', 'Developer\Admin\DeveloperProjectController@projectsExcelExport')->name('dev-admin.projects.export');

    // SECTION: Developer Admins
    Route::get('developer-admins', 'Developer\Admin\DeveloperAdminController@displayDeveloperAdmin')->name('dev-admin.developer-admins.index');
    Route::get('developer-admins/dt', 'Developer\Admin\DeveloperAdminController@getDataTableDeveloperAdmins')->name('dev-admin.developer-admins.dt');
    Route::get('developer-admins/export', 'Developer\Admin\DeveloperAdminController@getDeveloperAdminsExcelExport')->name('dev-admin.developer-admins.export')->where(['proj_id' => '[0-9]+']);
    Route::get('developer-admins/add', 'Developer\Admin\DeveloperAdminController@addDeveloperAdmin')->name('dev-admin.developer-admins.add');
    Route::post('developer-admins/add', 'Developer\Admin\DeveloperAdminController@postAddDeveloperAdmin')->name('dev-admin.developer-admins.add.post');
    Route::get('developer-admins/{id}/edit', 'Developer\Admin\DeveloperAdminController@editDeveloperAdmin')->name('dev-admin.developer-admins.edit')->where('id', '[0-9]+');
    Route::post('developer-admins/{id}/edit', 'Developer\Admin\DeveloperAdminController@postEditDeveloperAdmin')->name('dev-admin.developer-admins.edit.post')->where('id', '[0-9]+');
    Route::post('developer-admins/{id}/delete', 'Developer\Admin\DeveloperAdminController@deleteDeveloperAdmin')->name('dev-admin.developer-admins.delete')->where('id', '[0-9]+');
 
    // SECTION: Project Dashboard
    Route::get('projects/{proj_id}/dashboard', 'Developer\Admin\ProjectDashboardController@index')->name('dev-admin.projects.dashboard')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dashboard/stats/by-defects', 'Developer\Admin\ProjectDashboardController@ajaxGetByDefectsStats')->name('dev-admin.projects.dashboard.by-defects.ajax.get')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dashboard/stats/by-defect-types', 'Developer\Admin\ProjectDashboardController@ajaxGetByDefectTypeStats')->name('dev-admin.projects.dashboard.stats.by-defect-types.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-units', 'Developer\Admin\ProjectDashboardController@ajaxGetByUnitsStats')->name('dev-admin.projects.dashboard.stats.by-units.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-tags', 'Developer\Admin\ProjectDashboardController@ajaxGetByTagsStats')->name('dev-admin.projects.dashboard.stats.by-tags.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-response-times', 'Developer\Admin\ProjectDashboardController@ajaxDefectGetByResponseTimesStats')->name('dev-admin.projects.dashboard.stats.by-response-times.ajax.get')->where('proj_id', '[0-9]+');
    

    // SECTION: Assigned Developer Admins and COW
    Route::get('projects/{proj_id}/dev-admins', 'Developer\Admin\DeveloperProjectController@assignDeveloperAdmin')->name('dev-admin.projects.dev-admins.index')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dev-admins/dt', 'Developer\Admin\DeveloperProjectController@getDataTableAssignDeveloperAdmin')->name('dev-admin.projects.dev-admins.dt')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dev-admins/assign', 'Developer\Admin\DeveloperProjectController@addAssignDeveloperAdmin')->name('dev-admin.projects.dev-admins.assign')->where('proj_id', '[0-9]+');
    Route::post('projects/{proj_id}/dev-admins/assign', 'Developer\Admin\DeveloperProjectController@postAssignDeveloperAdmin')->name('dev-admin.projects.dev-admins.assign.post')->where('proj_id', '[0-9]+'); 

    Route::get('projects/{proj_id}/clerks-of-work', 'Developer\Admin\DeveloperProjectController@assignProjectClerkOfWork')->name('dev-admin.projects.dev-cows.index')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/clerks-of-work/dt', 'Developer\Admin\DeveloperProjectController@getDataTableAssignProjectClerkOfWork')->name('dev-admin.projects.dev-cows.dt')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/clerks-of-work/assign', 'Developer\Admin\DeveloperProjectController@addAssignProjectClerkOfWork')->name('dev-admin.projects.dev-cows.assign')->where('proj_id', '[0-9]+');
    Route::post('projects/{proj_id}/clerks-of-work/assign', 'Developer\Admin\DeveloperProjectController@postAssignProjectClerkOfWork')->name('dev-admin.projects.dev-cows.assign.post')->where('proj_id', '[0-9]+');    
    Route::post('projects/{proj_id}/dev-admins/{id}/unassign', 'Developer\Admin\DeveloperProjectController@postUnassignDeveloperAdmin')->name('dev-admin.projects.dev-admins.unassign')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/clerks-of-work/{id}/unassign', 'Developer\Admin\DeveloperProjectController@postUnassignProjectClerkOfWork')->name('dev-admin.projects.dev-cows.unassign')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    
    Route::get('contractor', 'Developer\Admin\ContractorController@index')->name('dev-admin.contractor.index');
    Route::get('contractor/dt', 'Developer\Admin\ContractorController@getDataTableContractor')->name('dev-admin.contractor.dt');
    Route::get('contractor/export', 'Developer\Admin\ContractorController@getContractorsExcelExport')->name('dev-admin.contractor.export');
   
    // SECTION: Clerk of Work
    Route::get('clerks-of-work', 'Developer\Admin\ClerkOfWorkController@index')->name('dev-admin.clerks-of-work.index');
    Route::get('clerks-of-work/dt', 'Developer\Admin\ClerkOfWorkController@getDataTableClerksOfWork')->name('dev-admin.clerks-of-work.dt');
    Route::get('clerks-of-work/export', 'Developer\Admin\ClerkOfWorkController@getClerkOfWorksExcelExport')->name('dev-admin.clerks-of-work.export');
    Route::get('clerks-of-work/add', 'Developer\Admin\ClerkOfWorkController@addClerkOfWork')->name('dev-admin.clerks-of-work.add');
    Route::post('clerks-of-work/add', 'Developer\Admin\ClerkOfWorkController@postAddClerkOfWork')->name('dev-admin.clerks-of-work.add.post');
    Route::get('clerks-of-work/{id}/edit', 'Developer\Admin\ClerkOfWorkController@editClerkOfWork')->name('dev-admin.clerks-of-work.edit')->where(['id' => '[0-9]+']);
    Route::post('clerks-of-work/{id}/edit', 'Developer\Admin\ClerkOfWorkController@postEditClerkOfWork')->name('dev-admin.clerks-of-work.edit.post')->where(['id' => '[0-9]+']);
    Route::post('clerks-of-work/{id}/delete', 'Developer\Admin\ClerkOfWorkController@deleteClerkOfWork')->name('dev-admin.clerks-of-work.delete')->where(['id' => '[0-9]+']);

    // SECTION: Units
    Route::get('projects/{proj_id}/units', 'Developer\Admin\UnitController@index')->name('dev-admin.projects.units.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/dt', 'Developer\Admin\UnitController@getDataTableUnits')->name('dev-admin.projects.units.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/create-excel', 'Developer\Admin\UnitController@createExcelUnitsTemplate')->name('dev-admin.projects.units.create-excel')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/import', 'Developer\Admin\UnitController@importUnits')->name('dev-admin.projects.units.import')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/import', 'Developer\Admin\UnitController@postUnitsExcelImport')->name('dev-admin.projects.units.import.post')->where(['proj_id' => '[0-9]+']);

    Route::get('projects/{proj_id}/units/export', 'Developer\Admin\UnitController@unitsExcelExport')->name('dev-admin.projects.units.export')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/add', 'Developer\Admin\UnitController@addUnit')->name('dev-admin.projects.units.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/add', 'Developer\Admin\UnitController@postAddUnit')->name('dev-admin.projects.units.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/{id}/edit', 'Developer\Admin\UnitController@editUnit')->name('dev-admin.projects.units.edit')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/{id}/edit', 'Developer\Admin\UnitController@postEditUnit')->name('dev-admin.projects.units.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/{id}/delete', 'Developer\Admin\UnitController@deleteUnit')->name('dev-admin.projects.units.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);

    // SECTION: Unit Types
    Route::get('projects/{proj_id}/unit-types', 'Developer\Admin\UnitTypeController@index')->name('dev-admin.projects.unit-types.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/dt', 'Developer\Admin\UnitTypeController@getDataTableUnitTypes')->name('dev-admin.projects.unit-types.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/create-excel', 'Developer\Admin\UnitTypeController@createExcelUnitTypesTemplate')->name('dev-admin.projects.unit-types.create-excel')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/import', 'Developer\Admin\UnitTypeController@importUnitTypes')->name('dev-admin.projects.unit-types.import')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/import', 'Developer\Admin\UnitTypeController@postUnitTypesExcelImport')->name('dev-admin.projects.unit-types.import.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/export', 'Developer\Admin\UnitTypeController@getUnitTypesExcelExport')->name('dev-admin.projects.unit-types.export')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/add', 'Developer\Admin\UnitTypeController@addUnitType')->name('dev-admin.projects.unit-types.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/add', 'Developer\Admin\UnitTypeController@postAddUnitType')->name('dev-admin.projects.unit-types.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{id}/edit', 'Developer\Admin\UnitTypeController@editUnitType')->name('dev-admin.projects.unit-types.edit')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/edit', 'Developer\Admin\UnitTypeController@postEditUnitType')->name('dev-admin.projects.unit-types.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/delete', 'Developer\Admin\UnitTypeController@deleteUnitType')->name('dev-admin.projects.unit-types.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor', 'Developer\Admin\UnitTypeController@postAddUnitTypeFloor')->name('dev-admin.projects.unit-types.floors.ajax.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor/edit', 'Developer\Admin\UnitTypeController@postEditUnitTypeFloor')->name('dev-admin.projects.unit-types.floors.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor/delete', 'Developer\Admin\UnitTypeController@postDeleteUnitTypefloor')->name('dev-admin.projects.unit-types.floors.delete.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}/floor/{id}/floor-plan', 'Developer\Admin\UnitTypeController@getUnitTypeFloorPlanImage')->name('dev-admin.projects.unit-types.floors.floor-plan.get')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+', 'id' => '[0-9]+']);

    Route::get('projects/{proj_id}/calendar', 'Developer\Admin\ProjectCalendarController@calendar')->name('dev-admin.projects.calendar')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/calendar/data', 'Developer\Admin\ProjectCalendarController@ajaxGetCalendarData')->name('dev-admin.projects.ajax.calendar.data.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/request/{status}', 'Developer\Admin\DefectController@ajaxGetCaseDefects')->name('dev-admin.projects.defects.ajax.status')->where('status', '[A-Za-z0-9\-\/]+');

    // SECTION: Cases
    Route::get('projects/{proj_id}/cases', 'Developer\Admin\CaseController@index')->name('dev-admin.projects.cases.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/dt', 'Developer\Admin\CaseController@getDataTableCases')->name('dev-admin.projects.cases.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/add', 'Developer\Admin\CaseController@addCase')->name('dev-admin.projects.cases.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/add', 'Developer\Admin\CaseController@postAddCase')->name('dev-admin.projects.cases.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{id}', 'Developer\Admin\CaseController@viewCase')->name('dev-admin.projects.cases.view')->where(['proj_id' => '[0-9]+',  'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/delete', 'Developer\Admin\CaseController@deleteCase')->name('dev-admin.projects.cases.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/status', 'Developer\Admin\CaseController@ajaxPostCaseStatus')->name('dev-admin.projects.cases.ajax.status.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/description', 'Developer\Admin\CaseController@ajaxPostCaseDescription')->name('dev-admin.projects.cases.ajax.description.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/tags', 'Developer\Admin\CaseController@ajaxPostCaseTags')->name('dev-admin.projects.cases.ajax.tags.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/assign-cow', 'Developer\Admin\CaseController@ajaxPostAssignCow')->name('dev-admin.projects.cases.ajax.assigned-cow.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{id}/report', 'Developer\Admin\CaseController@caseReportExport')->name('dev-admin.projects.cases.report')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/defects', 'Developer\Admin\DefectController@index')->name('dev-admin.projects.defects.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/defects/dt', 'Developer\Admin\DefectController@getDataTableDefects')->name('dev-admin.projects.defects.dt')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/defects/{defect_id}/search', 'Developer\Admin\DefectController@ajaxPostSearchDefects')->name('dev-admin.projects.defects.ajax.search.post')->where(['proj_id' => '[0-9]+', 'defect_id' => '[0-9]+']);
    
    // SECTION: Defects
    Route::get('projects/{proj_id}/cases/{case_id}/defects/', 'Developer\Admin\DefectController@ajaxGetDefects')->name('dev-admin.projects.cases.defects.ajax.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}', 'Developer\Admin\DefectController@ajaxGetDefectInfo')->name('dev-admin.projects.cases.defects.ajax.info')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects', 'Developer\Admin\DefectController@ajaxPostDefect')->name('dev-admin.projects.cases.defects.ajax.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/assign-contractor', 'Developer\Admin\DefectController@ajaxPostDefectContractor')->name('dev-admin.projects.cases.defects.ajax.assigned-contractor.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/status', 'Developer\Admin\DefectController@ajaxPostDefectStatus')->name('dev-admin.projects.cases.defects.ajax.status.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/duplicate-defect-info', 'Developer\Admin\DefectController@ajaxGetDuplicateDefectInfo')->name('dev-admin.projects.cases.defects.ajax.duplicate-defect-info.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/due-date', 'Developer\Admin\DefectController@ajaxPostDefectDueDate')->name('dev-admin.projects.cases.defects.ajax.due-date.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/extend-due-date', 'Developer\Admin\DefectController@ajaxPostDefectExtendDueDate')->name('dev-admin.projects.cases.defects.ajax.extend-due-date.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/defect-type', 'Developer\Admin\DefectController@ajaxPostDefectDefectType')->name('dev-admin.projects.cases.defects.ajax.defect-type.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/activities', 'Developer\Admin\DefectController@ajaxGetDefectActivities')->name('dev-admin.projects.cases.defects.ajax.activities')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/activities/{activity_id}/images/{id}', 'Developer\Admin\DefectController@ajaxGetDefectActivityImage')->name('dev-admin.projects.cases.defects.activities.images.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('defects/{defect_id}/activities/{activity_id}/profile-images/{id}', 'Developer\Admin\DefectController@ajaxGetDefectActivitiesUserProfileImage')->name('dev-admin.projects.cases.defects.activities.user-profile-images.get')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/activities/comment', 'Developer\Admin\DefectController@ajaxPostAddActivityComment')->name('dev-admin.projects.cases.defects.ajax.activities.comment.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/requests/{activity_id}/response', 'Developer\Admin\DefectController@ajaxPostRequestResponse')->name('dev-admin.projects.cases.defects.ajax.requests.response.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/description', 'Developer\Admin\DefectController@ajaxPostDescription')->name('dev-admin.projects.cases.defects.ajax.description.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/pins', 'Developer\Admin\DefectController@ajaxPostPins')->name('dev-admin.projects.cases.defects.ajax.pins.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/images', 'Developer\Admin\DefectController@ajaxPostDefectImage')->name('dev-admin.projects.cases.defects.ajax.images.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/tags', 'Developer\Admin\DefectController@ajaxPostDefectTags')->name('dev-admin.projects.cases.defects.ajax.tags.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/report', 'Developer\Admin\DefectController@defectReportExport')->name('dev-admin.projects.cases.defects.report')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}', 'Developer\Admin\DefectController@getDefectImage')->name('dev-admin.projects.cases.defects.images.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}/delete', 'Developer\Admin\DefectController@ajaxPostDeleteDefectImage')->name('dev-admin.projects.cases.defects.ajax.image.delete')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/assignees', 'Developer\Admin\ProjectAssigneeController@index')->name('dev-admin.projects.assignees.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/assignees/dt', 'Developer\Admin\ProjectAssigneeController@getDataTableAssignees')->name('dev-admin.projects.assignees.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/assignees/{user_id}/defects', 'Developer\Admin\ProjectAssigneeController@ajaxGetAssigneesDefects')->name('dev-admin.projects.assignees.defects.ajax.get')->where(['proj_id' => '[0-9]+', 'user_id' => '[0-9]+']);
    


    // SECTION: Defect Types
    Route::get('configuration/defect-type', 'Developer\Admin\Configuration\DefectTypeController@displayDefectType')->name('dev-admin.configuration.defect-types.index');
    Route::get('configuration/defect-type/dt', 'Developer\Admin\Configuration\DefectTypeController@getDataTableDefectType')->name('dev-admin.configuration.defect-types.dt');
    Route::get('configuration/defect-type/add', 'Developer\Admin\Configuration\DefectTypeController@addDefectType')->name('dev-admin.configuration.defect-types.add');
    Route::post('configuration/defect-type/add', 'Developer\Admin\Configuration\DefectTypeController@postAddDefectType')->name('dev-admin.configuration.defect-types.add.post');
    Route::get('configuration/defect-type/{id}/edit', 'Developer\Admin\Configuration\DefectTypeController@editDefectType')->name('dev-admin.configuration.defect-types.edit')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/edit', 'Developer\Admin\Configuration\DefectTypeController@postEditDefectType')->name('dev-admin.configuration.defect-types.edit.post')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/delete', 'Developer\Admin\Configuration\DefectTypeController@deleteDefectType')->name('dev-admin.configuration.defect-types.delete')->where(['id' => '[0-9]+']);

    // SECTION: Contractor Associations
    Route::get('associations', 'Developer\Admin\ContractorAssociationController@index')->name('dev-admin.associations.index');
    Route::get('associations/dt', 'Developer\Admin\ContractorAssociationController@getDataTableContractorAssociation')->name('dev-admin.associations.dt');
    Route::get('associations/export', 'Developer\Admin\ContractorAssociationController@getContractorAssociationsExcelExport')->name('dev-admin.associations.export');
    Route::get('associations/add', 'Developer\Admin\ContractorAssociationController@addContractorAssociation')->name('dev-admin.associations.add');
    Route::post('associations/add', 'Developer\Admin\ContractorAssociationController@postAddContractorAssociation')->name('dev-admin.associations.add.post');
    Route::get('associations/{id}/edit', 'Developer\Admin\ContractorAssociationController@editContractorAssociation')->name('dev-admin.associations.edit')->where(['id' => '[0-9]+']);
    Route::post('associations/{id}/edit', 'Developer\Admin\ContractorAssociationController@postEditContractorAssociation')->name('dev-admin.associations.edit.post')->where(['id' => '[0-9]+']);
    Route::get('associations/contractor-profile', 'Developer\Admin\ContractorAssociationController@getContractorProfile')->name('dev-admin.associations.contractor-profile');
    Route::post('associations/{id}/delete', 'Developer\Admin\ContractorAssociationController@deleteContractorAssociation')->name('dev-admin.associations.delete')->where(['id' => '[0-9]+']);
    Route::post('contractors/search', 'Developer\Admin\ContractorController@postSearchContractors')->name('dev-admin.contractors.ajax.search.post');

});

// APP: Contractor
Route::group(['prefix' => 'contractor', 'middleware' => ['auth', 'role:contractor']], function () {
    Route::get('', 'Contractor\ContractorController@dashboard')->name('contractor.dashboard')->where(['id' => '[0-9]+']);

    Route::get('defects/me', 'Contractor\DefectController@ajaxGetDefects')->name('contractor.defects.me.ajax.get');
    Route::get('defects/{id}', 'Contractor\DefectController@ajaxGetDefectInfo')->name('contractor.defects.ajax.info')->where(['id' => '[0-9]+']);
    Route::get('defects/{id}/activities', 'Contractor\DefectController@ajaxGetDefectActivities')->name('contractor.defects.ajax.activities')->where(['id' => '[0-9]+']);
    Route::get('defects/{defect_id}/images/{id}', 'Contractor\DefectController@getDefectImage')->name('contractor.defects.images.get')->where(['defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('defects/{defect_id}/activities/{activity_id}/images/{id}', 'Contractor\DefectController@ajaxGetDefectActivityImage')->name('contractor.defects.activities.images.get')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('defects/{defect_id}/activities/{activity_id}/profile-images/{id}', 'Contractor\DefectController@ajaxGetDefectActivitiesUserProfileImage')->name('contractor.defects.activities.user-profile-images.get')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('defects/{id}/activities/comment', 'Contractor\DefectController@ajaxPostAddActivityComment')->name('contractor.defects.ajax.activities.comment.post')->where(['id' => '[0-9]+']);
    Route::post('defects/{id}/status', 'Contractor\DefectController@ajaxPostDefectStatus')->name('contractor.defects.ajax.status.post')->where(['id' => '[0-9]+']);
    Route::post('defects/{id}/request', 'Contractor\DefectController@ajaxPostDefectRequest')->name('contractor.defects.ajax.request.post')->where(['id' => '[0-9]+']);
    Route::post('defects/{defect_id}/request/{activity_id}/cancel', 'Contractor\DefectController@ajaxPostDefectRequestCancel')->name('contractor.defects.ajax.request.cancel.post')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+']);

    // TODO Improve access
    Route::get('projects/{proj_id}/units/{unit_id}/floor/{floor_id}/floor-plan', 'Contractor\UnitController@getUnitFloorPlan')->name('contractor.projects.unit.floors.floor-plan.get')->where(['proj_id' => '[0-9]+', 'unit_id' => '[0-9]+', 'floor_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/{unit_id}/type', 'Contractor\UnitController@ajaxGetUnitUnitType')->name('contractor.projects.units.ajax.type.get')->where(['proj_id' => '[0-9]+', 'unit_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}/floor/{floor_id}/floor-plan', 'Contractor\UnitTypeController@getUnitTypeFloorPlanImage')->name('contractor.projects.unit-types.floors.floor-plan.get')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+', 'floor_id' => '[0-9]+']);

});

// APP: Developer COW
Route::group(['prefix' => 'dev-cow', 'middleware' => ['auth', 'role:cow']], function () {
    Route::get('', 'Developer\ClerkOfWork\DashboardController@index')->name('dev-cow.dashboard');
    Route::get('audit-log', 'Developer\ClerkOfWork\AuditLogController@index')->name('dev-cow.audit-log.index');

    // SECTION: Contractors
    Route::get('contractors', 'Developer\ClerkOfWork\ContractorController@displayContractor')->name('dev-cow.contractors.index');
    Route::get('contractors/dt', 'Developer\ClerkOfWork\ContractorController@getDataTableContractor')->name('dev-cow.contractors.dt');
    
    // SECTION: Developer Admins
    Route::get('developer-admins', 'Developer\ClerkOfWork\DeveloperAdminController@displayDeveloperAdmin')->name('dev-cow.developer-admins.index');
    Route::get('developer-admins/dt', 'Developer\ClerkOfWork\DeveloperAdminController@getDataTableDeveloperAdmins')->name('dev-cow.developer-admins.dt');
    Route::get('developer-admins/export', 'Developer\ClerkOfWork\DeveloperAdminController@getDeveloperAdminsExcelExport')->name('dev-cow.developer-admins.export')->where(['proj_id' => '[0-9]+']);

    // SECTION: Clerk of Work
    Route::get('clerks-of-work', 'Developer\ClerkOfWork\ClerkOfWorkController@index')->name('dev-cow.clerks-of-work.index');
    Route::get('clerks-of-work/dt', 'Developer\ClerkOfWork\ClerkOfWorkController@getDataTableClerksOfWork')->name('dev-cow.clerks-of-work.dt');
    Route::get('clerks-of-work/export', 'Developer\ClerkOfWork\ClerkOfWorkController@getClerkOfWorksExcelExport')->name('dev-cow.clerks-of-work.export');
    Route::get('clerks-of-work/add', 'Developer\ClerkOfWork\ClerkOfWorkController@addClerkOfWork')->name('dev-cow.clerks-of-work.add');
    Route::post('clerks-of-work/add', 'Developer\ClerkOfWork\ClerkOfWorkController@postAddClerkOfWork')->name('dev-cow.clerks-of-work.add.post');
    Route::get('clerks-of-work/{id}/edit', 'Developer\ClerkOfWork\ClerkOfWorkController@editClerkOfWork')->name('dev-cow.clerks-of-work.edit')->where(['id' => '[0-9]+']);
    Route::post('clerks-of-work/{id}/edit', 'Developer\ClerkOfWork\ClerkOfWorkController@postEditClerkOfWork')->name('dev-cow.clerks-of-work.edit.post')->where(['id' => '[0-9]+']);
    Route::post('clerks-of-work/{id}/delete', 'Developer\ClerkOfWork\ClerkOfWorkController@deleteClerkOfWork')->name('dev-cow.clerks-of-work.delete')->where(['id' => '[0-9]+']);

    // SECTION: Defect Types
    Route::get('configuration/defect-types', 'Developer\ClerkOfWork\Configuration\DefectTypeController@displayDefectType')->name('dev-cow.configuration.defect-types.index');
    Route::get('configuration/defect-types/dt', 'Developer\ClerkOfWork\Configuration\DefectTypeController@getDataTableDefectType')->name('dev-cow.configuration.defect-types.dt');
    Route::get('configuration/defect-type/add', 'Developer\ClerkOfWork\Configuration\DefectTypeController@addDefectType')->name('dev-cow.configuration.defect-types.add');
    Route::post('configuration/defect-type/add', 'Developer\ClerkOfWork\Configuration\DefectTypeController@postAddDefectType')->name('dev-cow.configuration.defect-types.add.post');
    Route::get('configuration/defect-type/{id}/edit', 'Developer\ClerkOfWork\Configuration\DefectTypeController@editDefectType')->name('dev-cow.configuration.defect-types.edit')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/edit', 'Developer\ClerkOfWork\Configuration\DefectTypeController@postEditDefectType')->name('dev-cow.configuration.defect-types.edit.post')->where(['id' => '[0-9]+']);
    Route::post('configuration/defect-type/{id}/delete', 'Developer\ClerkOfWork\Configuration\DefectTypeController@deleteDefectType')->name('dev-cow.configuration.defect-types.delete')->where(['id' => '[0-9]+']);

    // SECTION: Projects
    Route::get('projects', 'Developer\ClerkOfWork\ProjectController@displayProjects')->name('dev-cow.projects.index');
    Route::get('projects/{proj_id}/logo', 'Developer\ClerkOfWork\ProjectController@getProjectLogo')->name('dev-cow.projects.logo')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/export', 'Developer\ClerkOfWork\ProjectController@projectsExcelExport')->name('dev-cow.projects.export');
    Route::get('projects/dt', 'Developer\ClerkOfWork\ProjectController@getDataTableProjects')->name('dev-cow.projects.dt');

    // SECTION: Project Dashboard
    Route::get('projects/{proj_id}/dashboard', 'Developer\ClerkOfWork\ProjectDashboardController@index')->name('dev-cow.projects.dashboard')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dashboard/stats/by-defects', 'Developer\ClerkOfWork\ProjectDashboardController@ajaxGetByDefectsStats')->name('dev-cow.projects.dashboard.by-defects.ajax.get')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dashboard/stats/by-defect-types', 'Developer\ClerkOfWork\ProjectDashboardController@ajaxGetByDefectTypeStats')->name('dev-cow.projects.dashboard.stats.by-defect-types.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-units', 'Developer\ClerkOfWork\ProjectDashboardController@ajaxGetByUnitsStats')->name('dev-cow.projects.dashboard.stats.by-units.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-tags', 'Developer\ClerkOfWork\ProjectDashboardController@ajaxGetByTagsStats')->name('dev-cow.projects.dashboard.stats.by-tags.ajax.get')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/dashboard/stats/by-response-times', 'Developer\ClerkOfWork\ProjectDashboardController@ajaxDefectGetByResponseTimesStats')->name('dev-cow.projects.dashboard.stats.by-response-times.ajax.get')->where('proj_id', '[0-9]+');

    // SECTION: Units
    Route::get('projects/{proj_id}/units', 'Developer\ClerkOfWork\UnitController@index')->name('dev-cow.projects.units.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/dt', 'Developer\ClerkOfWork\UnitController@getDataTableUnits')->name('dev-cow.projects.units.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/export', 'Developer\ClerkOfWork\UnitController@unitsExcelExport')->name('dev-cow.projects.units.export')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/add', 'Developer\ClerkOfWork\UnitController@addUnit')->name('dev-cow.projects.units.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/add', 'Developer\ClerkOfWork\UnitController@postAddUnit')->name('dev-cow.projects.units.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/units/{id}/edit', 'Developer\ClerkOfWork\UnitController@editUnit')->name('dev-cow.projects.units.edit')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/{id}/edit', 'Developer\ClerkOfWork\UnitController@postEditUnit')->name('dev-cow.projects.units.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/units/{id}/delete', 'Developer\ClerkOfWork\UnitController@deleteUnit')->name('dev-cow.projects.units.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);

    // SECTION: Unit Types
    Route::get('projects/{proj_id}/unit-types', 'Developer\ClerkOfWork\UnitTypeController@index')->name('dev-cow.projects.unit-types.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/dt', 'Developer\ClerkOfWork\UnitTypeController@getDataTableUnitTypes')->name('dev-cow.projects.unit-types.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/create-excel', 'Developer\ClerkOfWork\UnitTypeController@createExcelUnitTypesTemplate')->name('dev-cow.projects.unit-types.create-excel')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/import', 'Developer\ClerkOfWork\UnitTypeController@importUnitTypes')->name('dev-cow.projects.unit-types.import')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/import', 'Developer\ClerkOfWork\UnitTypeController@postUnitTypesExcelImport')->name('dev-cow.projects.unit-types.import.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/export', 'Developer\ClerkOfWork\UnitTypeController@getUnitTypesExcelExport')->name('dev-cow.projects.unit-types.export')->where(['proj_id' => '[0-9]+']);;
    Route::get('projects/{proj_id}/unit-types/add', 'Developer\ClerkOfWork\UnitTypeController@addUnitType')->name('dev-cow.projects.unit-types.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/add', 'Developer\ClerkOfWork\UnitTypeController@postAddUnitType')->name('dev-cow.projects.unit-types.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{id}/edit', 'Developer\ClerkOfWork\UnitTypeController@editUnitType')->name('dev-cow.projects.unit-types.edit')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/edit', 'Developer\ClerkOfWork\UnitTypeController@postEditUnitType')->name('dev-cow.projects.unit-types.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/delete', 'Developer\ClerkOfWork\UnitTypeController@deleteUnitType')->name('dev-cow.projects.unit-types.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor', 'Developer\ClerkOfWork\UnitTypeController@postAddUnitTypeFloor')->name('dev-cow.projects.unit-types.floors.ajax.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor/edit', 'Developer\ClerkOfWork\UnitTypeController@postEditUnitTypeFloor')->name('dev-cow.projects.unit-types.floors.edit.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/unit-types/{id}/floor/delete', 'Developer\ClerkOfWork\UnitTypeController@postDeleteUnitTypeFloor')->name('dev-cow.projects.unit-types.floors.delete.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/unit-types/{unit_type_id}/floor/{id}/floor-plan', 'Developer\ClerkOfWork\UnitTypeController@getUnitTypeFloorPlanImage')->name('dev-cow.projects.unit-types.floors.floor-plan.get')->where(['proj_id' => '[0-9]+', 'unit_type_id' => '[0-9]+', 'id' => '[0-9]+']);
    
    // SECTION: Case and Defect
    Route::get('projects/{proj_id}/calendar', 'Developer\ClerkOfWork\ProjectCalendarController@calendar')->name('dev-cow.projects.calendar')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/calendar/data', 'Developer\ClerkOfWork\ProjectCalendarController@ajaxGetCalendarData')->name('dev-cow.projects.ajax.calendar.data.get')->where(['proj_id' => '[0-9]+']);

    Route::get('projects/{proj_id}/cases', 'Developer\ClerkOfWork\CaseController@index')->name('dev-cow.projects.cases.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/dt', 'Developer\ClerkOfWork\CaseController@getDataTableCases')->name('dev-cow.projects.cases.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/add', 'Developer\ClerkOfWork\CaseController@addCase')->name('dev-cow.projects.cases.add')->where(['proj_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/add', 'Developer\ClerkOfWork\CaseController@postAddCase')->name('dev-cow.projects.cases.add.post')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{id}', 'Developer\ClerkOfWork\CaseController@viewCase')->name('dev-cow.projects.cases.view')->where(['proj_id' => '[0-9]+',  'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/delete', 'Developer\ClerkOfWork\CaseController@deleteCase')->name('dev-cow.projects.cases.delete')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/status', 'Developer\ClerkOfWork\CaseController@ajaxPostCaseStatus')->name('dev-cow.projects.cases.ajax.status.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/description', 'Developer\ClerkOfWork\CaseController@ajaxPostCaseDescription')->name('dev-cow.projects.cases.ajax.description.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/tags', 'Developer\ClerkOfWork\CaseController@ajaxPostCaseTags')->name('dev-cow.projects.cases.ajax.tags.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{id}/assign-cow', 'Developer\ClerkOfWork\CaseController@ajaxPostAssignCow')->name('dev-cow.projects.cases.ajax.assigned-cow.post')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{id}/report', 'Developer\ClerkOfWork\CaseController@caseReportExport')->name('dev-cow.projects.cases.report')->where(['proj_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/defects', 'Developer\ClerkOfWork\DefectController@index')->name('dev-cow.projects.defects.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/', 'Developer\ClerkOfWork\DefectController@ajaxGetDefects')->name('dev-cow.projects.cases.defects.ajax.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}', 'Developer\ClerkOfWork\DefectController@ajaxGetDefectInfo')->name('dev-cow.projects.cases.defects.ajax.info')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects', 'Developer\ClerkOfWork\DefectController@ajaxPostDefect')->name('dev-cow.projects.cases.defects.ajax.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/assign-contractor', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectContractor')->name('dev-cow.projects.cases.defects.ajax.assigned-contractor.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/status', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectStatus')->name('dev-cow.projects.cases.defects.ajax.status.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/duplicate-defect-info', 'Developer\ClerkOfWork\DefectController@ajaxGetDuplicateDefectInfo')->name('dev-cow.projects.cases.defects.ajax.duplicate-defect-info.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/due-date', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectDueDate')->name('dev-cow.projects.cases.defects.ajax.due-date.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/extend-due-date', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectExtendDueDate')->name('dev-cow.projects.cases.defects.ajax.extend-due-date.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/defect-type', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectDefectType')->name('dev-cow.projects.cases.defects.ajax.defect-type.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/activities', 'Developer\ClerkOfWork\DefectController@ajaxGetDefectActivities')->name('dev-cow.projects.cases.defects.ajax.activities')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/activities/{activity_id}/images/{id}', 'Developer\ClerkOfWork\DefectController@ajaxGetDefectActivityImage')->name('dev-cow.projects.cases.defects.activities.images.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('defects/{defect_id}/activities/{activity_id}/profile-images/{id}', 'Developer\ClerkOfWork\DefectController@ajaxGetDefectActivitiesUserProfileImage')->name('dev-cow.projects.cases.defects.activities.user-profile-images.get')->where(['defect_id' => '[0-9]+', 'activity_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/activities/comment', 'Developer\ClerkOfWork\DefectController@ajaxPostAddActivityComment')->name('dev-cow.projects.cases.defects.ajax.activities.comment.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/requests/{activity_id}/response', 'Developer\ClerkOfWork\DefectController@ajaxPostRequestResponse')->name('dev-cow.projects.cases.defects.ajax.requests.response.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'activity_id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/description', 'Developer\ClerkOfWork\DefectController@ajaxPostDescription')->name('dev-cow.projects.cases.defects.ajax.description.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/pins', 'Developer\ClerkOfWork\DefectController@ajaxPostPins')->name('dev-cow.projects.cases.defects.ajax.pins.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/images', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectImage')->name('dev-cow.projects.cases.defects.ajax.images.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{id}/tags', 'Developer\ClerkOfWork\DefectController@ajaxPostDefectTags')->name('dev-cow.projects.cases.defects.ajax.tags.post')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{id}/report', 'Developer\ClerkOfWork\DefectController@defectReportExport')->name('dev-cow.projects.cases.defects.report')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::get('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}', 'Developer\ClerkOfWork\DefectController@getDefectImage')->name('dev-cow.projects.cases.defects.images.get')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);
    Route::post('projects/{proj_id}/cases/{case_id}/defects/{defect_id}/images/{id}/delete', 'Developer\ClerkOfWork\DefectController@ajaxPostDeleteDefectImage')->name('dev-cow.projects.cases.defects.ajax.image.delete')->where(['proj_id' => '[0-9]+', 'case_id' => '[0-9]+', 'defect_id' => '[0-9]+', 'id' => '[0-9]+']);

    // SECTION: Assigned Developer Admins and COW
    Route::get('projects/{proj_id}/dev-admins', 'Developer\ClerkOfWork\DeveloperProjectController@assignDeveloperAdmin')->name('dev-cow.projects.dev-admins.index')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/dev-admins/dt', 'Developer\ClerkOfWork\DeveloperProjectController@getDataTableAssignDeveloperAdmin')->name('dev-cow.projects.dev-admins.dt')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/clerks-of-work', 'Developer\ClerkOfWork\DeveloperProjectController@assignProjectClerkOfWork')->name('dev-cow.projects.dev-cows.index')->where('proj_id', '[0-9]+');
    Route::get('projects/{proj_id}/clerks-of-work/dt', 'Developer\ClerkOfWork\DeveloperProjectController@getDataTableAssignProjectClerkOfWork')->name('dev-cow.projects.dev-cows.dt')->where('proj_id', '[0-9]+');
    
    Route::get('projects/{proj_id}/assignees', 'Developer\ClerkOfWork\ProjectAssigneeController@index')->name('dev-cow.projects.assignees.index')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/assignees/dt', 'Developer\ClerkOfWork\ProjectAssigneeController@getDataTableAssignees')->name('dev-cow.projects.assignees.dt')->where(['proj_id' => '[0-9]+']);
    Route::get('projects/{proj_id}/assignees/{user_id}/defects', 'Developer\ClerkOfWork\ProjectAssigneeController@ajaxGetAssigneesDefects')->name('dev-cow.projects.assignees.defects.ajax.get')->where(['proj_id' => '[0-9]+', 'user_id' => '[0-9]+']);

    // SECTION: Developer Contractor Associations
    Route::get('associations', 'Developer\ClerkOfWork\ContractorAssociationController@index')->name('dev-cow.associations.index');
    Route::get('associations/dt', 'Developer\ClerkOfWork\ContractorAssociationController@getDataTableContractorAssociation')->name('dev-cow.associations.dt');
    Route::get('associations/export', 'Developer\ClerkOfWork\ContractorAssociationController@getContractorAssociationsExcelExport')->name('dev-cow.associations.export');
    Route::get('associations/add', 'Developer\ClerkOfWork\ContractorAssociationController@addContractorAssociation')->name('dev-cow.associations.add');
    Route::post('associations/add', 'Developer\ClerkOfWork\ContractorAssociationController@postAddContractorAssociation')->name('dev-cow.associations.add.post');
    Route::get('associations/{id}/edit', 'Developer\ClerkOfWork\ContractorAssociationController@editContractorAssociation')->name('dev-cow.associations.edit')->where(['id' => '[0-9]+']);
    Route::post('associations/{id}/edit', 'Developer\ClerkOfWork\ContractorAssociationController@postEditContractorAssociation')->name('dev-cow.associations.edit.post')->where(['id' => '[0-9]+']);
    Route::get('associations/contractor-profile', 'Developer\ClerkOfWork\ContractorAssociationController@getContractorProfile')->name('dev-cow.associations.contractor-profile');
    Route::post('associations/{id}/delete', 'Developer\ClerkOfWork\ContractorAssociationController@deleteContractorAssociation')->name('dev-cow.associations.delete')->where(['id' => '[0-9]+']);

    Route::post('contractors/search', 'Developer\ClerkOfWork\ContractorController@postSearchContractors')->name('dev-cow.contractors.ajax.search.post');

    Route::post('projects/{proj_id}/defects/{defect_id}/search', 'Developer\ClerkOfWork\DefectController@ajaxPostSearchDefects')->name('dev-cow.projects.defects.ajax.search.post')->where(['proj_id' => '[0-9]+', 'defect_id' => '[0-9]+']);
});

Route::get('/first-time-login', 'Auth\LoginController@firstTimeLogin')->name('first-time-login');
Route::get('/register/verify-user/{token}', 'Auth\RegisterController@verifyUser')->name('register.verify-user');
// Route::post('/register/verify-user/{token}', 'Auth\RegisterController@postVerifyUser');
Route::post('auth/change-password', 'Auth\LoginController@postChangePassword')->name('change-password');
Route::post('reset-user-password/{user_id}', 'Auth\ResetPasswordController@postResetUserPassword')->name('reset-user-password')->where(['user_id' => '[0-9]+']);
Route::post('auth/register-contractor', 'Auth\RegisterController@postAddContractor')->name('register.contractor');


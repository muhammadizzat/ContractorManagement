<?php

use App\DefectType;
use App\ClerkOfWork;
use App\ProjectCase;
use App\Developer;
use App\Project;
use App\User;
use App\Unit;
use App\UnitType;
use App\DeveloperContractorAssociation;
use Illuminate\Support\Str;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// SECTION: USER
Breadcrumbs::register('profile.edit', function ($breadcrumbs) {
    $breadcrumbs->push('Edit User Profile', route('profile.edit'));
});

Breadcrumbs::register('profile.password', function ($breadcrumbs) {
    $breadcrumbs->push('Edit User Password', route('profile.password'));
});

Breadcrumbs::register('notifications.page', function ($breadcrumbs) {
    $breadcrumbs->push('Notifications Page', route('notifications.page'));
});

// SECTION: LZ Admin 
Breadcrumbs::register('admin.dashboard', function ($breadcrumbs) {
    $breadcrumbs->push('Dashboard', route('admin.dashboard'));
});

Breadcrumbs::register('admin.audit-log.index', function ($breadcrumbs) {
    $breadcrumbs->push('Audit Log', route('admin.audit-log.index'));
});

Breadcrumbs::register('admin.users.index', function ($breadcrumbs) {
    $breadcrumbs->push('Users Management', route('admin.users.index'));
});

Breadcrumbs::register('admin.users.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('admin.users.index');
    $breadcrumbs->push('Edit User Profile', route('admin.users.edit', ['id' => $id]));
});

Breadcrumbs::register('admin.admins.index', function ($breadcrumbs) {
    $breadcrumbs->push('Linkzzapp Admin Management', route('admin.admins.index'));
});

Breadcrumbs::register('admin.admins.add', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.admins.index');
    $breadcrumbs->push('Add', route('admin.admins.index'));
});

Breadcrumbs::register('admin.admins.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('admin.admins.index');
    $admin = User::findOrFail($id);
    $breadcrumbs->push('Linkzzapp Edit (' . $admin->name . ')', route('admin.admins.index'));
});

Breadcrumbs::register('admin.developers.index', function ($breadcrumbs) {
    $breadcrumbs->push('Developer Management', route('admin.developers.index'));
});

Breadcrumbs::register('admin.developers.add', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.developers.index');
    $breadcrumbs->push('Add', route('admin.developers.index'));
});

Breadcrumbs::register('admin.developers.edit', function ($breadcrumbs, $dev_id) {
    $developer = Developer::findOrFail($dev_id);
    $breadcrumbs->parent('admin.developers.index');
    $breadcrumbs->push('Edit (' . $developer->name . ')', route('admin.developers.index'));
});

Breadcrumbs::register('admin.developers.projects.index', function ($breadcrumbs, $dev_id) {
    $developer = Developer::findOrFail($dev_id);
    $breadcrumbs->push($developer->name, route('admin.developers.index'), ['developer_bc' => true, 'developer_logo' => 'logo']);
    $breadcrumbs->push('Projects', route('admin.developers.projects.index', ['dev_id' => $dev_id]));
});

Breadcrumbs::register('admin.developers.projects.add', function ($breadcrumbs, $dev_id) {
    $breadcrumbs->parent('admin.developers.projects.index', $dev_id); //parent project list
    $breadcrumbs->push('Add', route('admin.developers.projects.add', ['dev_id' => $dev_id]));
});

Breadcrumbs::register('admin.developers.projects.view', function ($breadcrumbs, $dev_id, $id) {
    $developer = Developer::findOrFail($dev_id);
    $breadcrumbs->push($developer->name, route('admin.developers.index'), ['developer_bc' => true, 'developer_logo' => 'logo']);
    $project = Project::findOrFail($id);
    $breadcrumbs->push($project->name,route('admin.developers.projects.view', ['project_bc' => true, 'project_logo' => 'logo','dev_id' => $dev_id, 'id' => $id]));
});

Breadcrumbs::register('admin.developers.projects.edit', function ($breadcrumbs, $dev_id, $id) {
    $project = Project::findOrFail($id);
    $breadcrumbs->parent('admin.developers.projects.view', $dev_id, $id); //parent project view
    $breadcrumbs->push('Edit (' . $project->name . ')', route('admin.developers.projects.edit', ['dev_id' => $dev_id, 'id' => $id]));
});

Breadcrumbs::register('admin.developers.admins.index', function ($breadcrumbs, $dev_id) {
    $developer = Developer::findOrFail($dev_id);
    $breadcrumbs->push($developer->name, route('admin.developers.index'), ['developer_bc' => true, 'developer_logo' => 'logo']);
    $breadcrumbs->push('Developer Admins', route('admin.developers.admins.index', ['dev_id' => $dev_id]));
});

Breadcrumbs::register('admin.developer-admins.index', function ($breadcrumbs) {
    $breadcrumbs->push('Developers Admin', route('admin.admins.index'));
});

Breadcrumbs::register('admin.developer-admins.edit', function ($breadcrumbs, $id) {
    $admin = User::findOrFail($id);
    $breadcrumbs->parent('admin.developer-admins.index'); 
    $breadcrumbs->push('Edit (' . $admin->name . ')', route('admin.developer-admins.edit', ['id' => $id]));
});

Breadcrumbs::register('admin.developers.admins.add', function ($breadcrumbs, $dev_id) {
    $breadcrumbs->parent('admin.developers.admins.index', $dev_id); //parent dev-admin list 
    $breadcrumbs->push('Add', route('admin.developers.admins.add', ['dev_id' => $dev_id]));
});

Breadcrumbs::register('admin.developers.admins.edit', function ($breadcrumbs, $dev_id, $id) {
    $admin = User::findOrFail($id);
    $breadcrumbs->parent('admin.developers.admins.index', $dev_id); //parent dev-admin list 
    $breadcrumbs->push('Edit (' . $admin->name . ')', route('admin.developers.admins.edit', ['dev_id' => $dev_id, 'id' => $id]));
});

Breadcrumbs::register('admin.contractors.index', function ($breadcrumbs) {
    $breadcrumbs->push('Contractors', route('admin.admins.index')); //parent contractors list 
});

Breadcrumbs::register('admin.contractors.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('admin.contractors.index');
    $contractor = User::findOrFail($id);
    $breadcrumbs->push('Edit (' . $contractor->name .')', route('admin.contractors.edit', ['id' => $id])); //parent contractors list 
});

Breadcrumbs::register('admin.configuration.defect-types.index', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.dashboard'); //parent dashboard
    $breadcrumbs->push('Defect Types', route('admin.configuration.defect-types.index'));
});

Breadcrumbs::register('admin.configuration.defect-types.add', function ($breadcrumbs) {
    $breadcrumbs->parent('admin.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Add', route('admin.configuration.defect-types.add'));
});

Breadcrumbs::register('admin.configuration.defect-types.edit', function ($breadcrumbs, $id) {
    $defectType = DefectType::findOrFail($id);
    $breadcrumbs->parent('admin.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Edit (' . $defectType->title . ')', route('admin.configuration.defect-types.edit',  ['id' => $id]));
});

// SECTION: Contractor
Breadcrumbs::register('contractor.dashboard', function ($breadcrumbs) {
    $breadcrumbs->push('Dashboard', route('contractor.dashboard')); //parent dashboard
});

// SECTION: Developer Admin
Breadcrumbs::register('dev-admin.dashboard', function ($breadcrumbs) {
    $developer = Developer::findOrFail(request()->user()->developer_admin->developer_id);
    $breadcrumbs->push($developer->name, route('dev-admin.dashboard'), ['developer_bc' => true, 'developer_logo' => 'logo']);
});

Breadcrumbs::register('dev-admin.audit-log.index', function ($breadcrumbs) {
    $breadcrumbs->push('Audit Log', route('dev-admin.audit-log.index'));
});

Breadcrumbs::register('dev-admin.projects.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard') ; //parent dashboard
    $breadcrumbs->push('Projects', route('dev-admin.projects.index'));
});

Breadcrumbs::register('dev-admin.projects.dashboard', function ($breadcrumbs,$proj_id) {
    $project = Project::findOrFail($proj_id);
    $developer = Developer::findOrFail(request()->user()->developer_admin->developer_id);
    $breadcrumbs->push($developer->name, route('dev-admin.projects.index'), ['developer_bc' => true, 'developer_logo' => 'logo']);
    $breadcrumbs->push($project->name, route('dev-admin.projects.dashboard', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.developer-admins.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard') ; //parent dashboard
    $breadcrumbs->push('Developer Admins', route('dev-admin.developer-admins.index'));
});

Breadcrumbs::register('dev-admin.developer-admins.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.developer-admins.index') ; //parent dev-admin list
    $breadcrumbs->push('Add', route('dev-admin.developer-admins.add'));
});

Breadcrumbs::register('dev-admin.developer-admins.edit', function ($breadcrumbs, $id) {
    $developerAdmin = User::findOrFail($id);
    $breadcrumbs->parent('dev-admin.developer-admins.index'); //parent dev-admin list 
    $breadcrumbs->push('Edit (' . $developerAdmin->name . ')', route('dev-admin.developer-admins.edit', ['id' => $id]));
});

Breadcrumbs::register('dev-admin.associations.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard') ;//parent dashboard
    $breadcrumbs->push('Setting Up Contractor Scope of Work', route('dev-admin.associations.index'));
});

Breadcrumbs::register('dev-admin.associations.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.associations.index');//parent contractor associations list
    $breadcrumbs->push('Add Contractor', route('dev-admin.associations.add'));
});

Breadcrumbs::register('dev-admin.associations.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('dev-admin.associations.index'); //parent contractor associations list
    $contractor_user = DeveloperContractorAssociation::findOrFail($id)->user;
    $contractor = User::findOrFail($contractor_user->id);
    $breadcrumbs->push('Edit (' . $contractor->name . ')', route('dev-admin.associations.edit', ['id' => $id]));
});

Breadcrumbs::register('dev-admin.contractor.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard') ; //parent dashboard
    $breadcrumbs->push('Contractor List', route('dev-admin.contractor.index'));
});

Breadcrumbs::register('dev-admin.clerks-of-work.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard') ; //parent dashboard
    $breadcrumbs->push('Clerk of Work List', route('dev-admin.clerks-of-work.index'));
});

Breadcrumbs::register('dev-admin.clerks-of-work.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.clerks-of-work.index') ; //parent cow list
    $breadcrumbs->push('Add', route('dev-admin.clerks-of-work.add'));
});

Breadcrumbs::register('dev-admin.clerks-of-work.edit', function ($breadcrumbs, $id) {
    $clerkOfWork = User::findOrFail($id);
    $breadcrumbs->parent('dev-admin.clerks-of-work.index') ; //parent cow list
    $breadcrumbs->push('Edit (' . $clerkOfWork->name . ')', route('dev-admin.clerks-of-work.edit', ['id' => $id]));
});

Breadcrumbs::register('dev-admin.projects.units.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Units', route('dev-admin.projects.units.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.units.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.units.index', $proj_id); //parent project unit list
    $breadcrumbs->push('Add', route('dev-admin.projects.units.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.units.import', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.units.index', $proj_id); //parent unit types list
    $breadcrumbs->push('Import', route('dev-admin.projects.units.import', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.units.edit', function ($breadcrumbs, $proj_id, $id) {
    $unit = Unit::findOrFail($id);
    $breadcrumbs->parent('dev-admin.projects.units.index', $proj_id); //parent project unit list
    $breadcrumbs->push('Edit (' . $unit->unit_no . ')', route('dev-admin.projects.units.index', ['proj_id' => $proj_id, 'id'=>$id]));
});

Breadcrumbs::register('dev-admin.projects.unit-types.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Unit Types', route('dev-admin.projects.unit-types.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.unit-types.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.unit-types.index', $proj_id); //parent unit type list
    $breadcrumbs->push('Add', route('dev-admin.projects.unit-types.add', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.unit-types.edit', function ($breadcrumbs, $proj_id, $id) {
    $unitType = UnitType::findOrFail($id);
    $breadcrumbs->parent('dev-admin.projects.unit-types.index', $proj_id); //parent unit type list
    $breadcrumbs->push('Edit  (' . $unitType->name . ')',route('dev-admin.projects.unit-types.edit', ['proj_id' => $proj_id, 'id' => $id]));
});

Breadcrumbs::register('dev-admin.projects.unit-types.import', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.unit-types.index', $proj_id); //parent unit types list
    $breadcrumbs->push('Import', route('dev-admin.projects.unit-types.import', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.cases.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Cases', route('dev-admin.projects.cases.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.cases.view', function ($breadcrumbs, $proj_id, $id) {
    $case = ProjectCase::findOrFail($id);
    $breadcrumbs->parent('dev-admin.projects.cases.index', $proj_id, $id); //parent case list
    $case_title = Str::limit($case->title, 10);
    $breadcrumbs->push('Case  (' . $case_title. ')', route('dev-admin.projects.cases.view', ['proj_id' => $proj_id, 'id' => $id]));
});

Breadcrumbs::register('dev-admin.projects.defects.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project-view
    $breadcrumbs->push('Defects', route('dev-admin.projects.defects.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.cases.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.cases.index', $proj_id); //parent case list
    $breadcrumbs->push('Add', route('dev-admin.projects.cases.index', ['proj_id' => $proj_id]));
});


Breadcrumbs::register('dev-admin.projects.dev-admins.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assigned Developer Admins', route('dev-admin.projects.dev-admins.index', ['proj_id' => $proj_id]));
});
Breadcrumbs::register('dev-admin.projects.dev-admins.assign', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id);  //parent project view
    $breadcrumbs->push('Assign Developer Admin', route('dev-admin.projects.dev-admins.assign', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.projects.dev-cows.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assigned Clerks Of Work', route('dev-admin.projects.dev-cows.index', ['proj_id' => $proj_id]));
});
Breadcrumbs::register('dev-admin.projects.dev-cows.assign', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assign Clerks Of Work', route('dev-admin.projects.dev-cows.assign', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-admin.configuration.defect-types.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.dashboard'); //parent dashboard
    $breadcrumbs->push('Defect Types', route('dev-admin.configuration.defect-types.index'));
});

Breadcrumbs::register('dev-admin.configuration.defect-types.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-admin.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Add', route('dev-admin.configuration.defect-types.add'));
});

Breadcrumbs::register('dev-admin.configuration.defect-types.edit', function ($breadcrumbs, $id) {
    $defectType = DefectType::findOrFail($id);
    $breadcrumbs->parent('dev-admin.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Edit (' . $defectType->title . ')', route('dev-admin.configuration.defect-types.edit',  ['id' => $id]));
});

Breadcrumbs::register('dev-admin.projects.calendar', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.index', $proj_id);
    $breadcrumbs->push("Calendar", route('dev-admin.projects.calendar', ['proj_id' => $proj_id]));
});
Breadcrumbs::register('dev-admin.projects.assignees.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-admin.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assignees', route('dev-admin.projects.assignees.index', ['proj_id' => $proj_id]));
});

//SECTION: DEV-COW

//SUB-SECTION: HOMEPAGE
Breadcrumbs::register('dev-cow.dashboard', function ($breadcrumbs) {
    $developer=Developer::findOrFail(request()->user()->clerk_of_work->developer_id); 
    $breadcrumbs->push($developer->name, route('dev-cow.dashboard'),['developer_bc' =>true, 'developer_logo' =>'logo']);
});

Breadcrumbs::register('dev-cow.audit-log.index', function ($breadcrumbs) {
    $breadcrumbs->push('Audit Log', route('dev-cow.audit-log.index'));
});

Breadcrumbs::register('dev-cow.developer-admins.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.dashboard') ; //parent dashboard
    $breadcrumbs->push('Developer Admins', route('dev-cow.developer-admins.index'));
});

//SUB-SECTION: CONTRACTORS
Breadcrumbs::register('dev-cow.contractors.index', function ($breadcrumbs) {
    $breadcrumbs->push('Contractors', route('admin.developers.index')); //parent contractor list
});

Breadcrumbs::register('dev-cow.associations.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.dashboard');//parent dashboard
    $breadcrumbs->push('Setting Up Contractor Scope of Work', route('dev-cow.associations.index'));
});

Breadcrumbs::register('dev-cow.associations.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.associations.index'); //parent contractor association list
    $breadcrumbs->push('Add Contractor', route('dev-cow.associations.add'));
});

Breadcrumbs::register('dev-cow.associations.edit', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('dev-cow.associations.index'); //parent contractor association list
    $contractor_user = DeveloperContractorAssociation::findOrFail($id)->user;
    $contractor = User::findOrFail($contractor_user->id);
    $breadcrumbs->push('Edit (' . $contractor->name . ')', route('dev-cow.associations.edit',  ['id' => $id]));
});

//SUB-SECTION: PROJECTS
Breadcrumbs::register('dev-cow.projects.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.dashboard');
    $breadcrumbs->push('Projects', route('dev-cow.projects.index'));
});

Breadcrumbs::register('dev-cow.projects.dashboard', function ($breadcrumbs,$proj_id) {
    $project = Project::findOrFail($proj_id);
    $developer = Developer::findOrFail(request()->user()->clerk_of_work->developer_id);
    $breadcrumbs->push($developer->name, route('dev-cow.projects.index'), ['developer_bc' => true, 'developer_logo' => 'logo']);
    $breadcrumbs->push($project->name,route('dev-cow.projects.dashboard', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.defects.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project-view
    $breadcrumbs->push('Defects', route('dev-cow.projects.defects.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.cases.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project-view
    $breadcrumbs->push('Cases', route('dev-cow.projects.cases.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.cases.view', function ($breadcrumbs, $proj_id, $id) {
    $case = ProjectCase::findOrFail($id);
    $breadcrumbs->parent('dev-cow.projects.cases.index', $proj_id, $id); //parent project-cases list
    $case_title = Str::limit($case->title, 10);
    $breadcrumbs->push('Case  (' . $case_title . ')', route('dev-cow.projects.cases.view', ['proj_id' => $proj_id, 'id' => $id]));
});

Breadcrumbs::register('dev-cow.projects.cases.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.cases.index', $proj_id); //parent project-cases list
    $breadcrumbs->push('Add', route('dev-cow.projects.cases.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.units.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project-view
    $breadcrumbs->push('Units', route('dev-cow.projects.units.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.units.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.units.index', $proj_id); //parent unit list
    $breadcrumbs->push('Add', route('dev-cow.projects.units.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.units.edit', function ($breadcrumbs, $proj_id, $id) {
    $unit = Unit::findOrFail($id);
    $breadcrumbs->parent('dev-cow.projects.units.index', $proj_id); //parent unit list
    $breadcrumbs->push('Edit (' . $unit->unit_no . ')',route('dev-cow.projects.units.index', ['proj_id' => $proj_id, 'id' =>$id]));
});

Breadcrumbs::register('dev-cow.projects.unit-types.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project-view
    $breadcrumbs->push('Unit Types', route('dev-cow.projects.unit-types.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.unit-types.add', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.unit-types.index',$proj_id) ; //parent unit-types list
    $breadcrumbs->push('Add', route('dev-cow.projects.unit-types.add', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.unit-types.edit', function ($breadcrumbs, $proj_id, $id) {
    $unitType = UnitType::findOrFail($id);
    $breadcrumbs->parent('dev-cow.projects.unit-types.index',$proj_id); //parent unit-types list
    $breadcrumbs->push('Edit  (' . $unitType->name . ')',route('dev-cow.projects.unit-types.edit', ['proj_id' => $proj_id,'id' =>$id]));
});

Breadcrumbs::register('dev-cow.projects.unit-types.import', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.unit-types.index', $proj_id); //parent unit-types list
    $breadcrumbs->push('Import', route('dev-cow.projects.unit-types.import', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.dev-admins.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assigned Developer Admins', route('dev-cow.projects.dev-admins.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.dev-cows.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assigned Clerks Of Work', route('dev-cow.projects.dev-cows.index', ['proj_id' => $proj_id]));
});
Breadcrumbs::register('dev-cow.projects.dev-cows.assign', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assign Clerks Of Work', route('dev-cow.projects.dev-cows.assign', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.clerks-of-work.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.dashboard'); //parent dashboard
    $breadcrumbs->push('Assigned Clerks of Work', route('dev-cow.clerks-of-work.index'));
});

Breadcrumbs::register('dev-cow.clerks-of-work.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.clerks-of-work.index');//parent cow list
    $breadcrumbs->push('Add', route('dev-cow.clerks-of-work.add'));
});

Breadcrumbs::register('dev-cow.projects.calendar', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.cases.index', $proj_id);
    $breadcrumbs->push("Calendar", route('dev-cow.projects.calendar', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.projects.assignees.index', function ($breadcrumbs, $proj_id) {
    $breadcrumbs->parent('dev-cow.projects.dashboard', $proj_id); //parent project view
    $breadcrumbs->push('Assignees', route('dev-cow.projects.assignees.index', ['proj_id' => $proj_id]));
});

Breadcrumbs::register('dev-cow.clerks-of-work.edit', function ($breadcrumbs, $id) {
    $clerkOfWork = User::findOrFail($id);
    $breadcrumbs->parent('dev-cow.clerks-of-work.index'); //parent cow list
    $breadcrumbs->push('Edit (' . $clerkOfWork->name . ')', route('dev-cow.clerks-of-work.edit', ['id' => $id]));
});

//SUB-SECTION DEFECT-TYPES

Breadcrumbs::register('dev-cow.configuration.defect-types.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.dashboard'); //parent dashboard
    $breadcrumbs->push('Defect Types', route('dev-cow.configuration.defect-types.index'));
});

Breadcrumbs::register('dev-cow.configuration.defect-types.add', function ($breadcrumbs) {
    $breadcrumbs->parent('dev-cow.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Add', route('dev-cow.configuration.defect-types.add'));
});

Breadcrumbs::register('dev-cow.configuration.defect-types.edit', function ($breadcrumbs, $id) {
    $defectType = DefectType::findOrFail($id);
    $breadcrumbs->parent('dev-cow.configuration.defect-types.index'); //parent defect type list
    $breadcrumbs->push('Edit (' . $defectType->title . ')', route('dev-cow.configuration.defect-types.edit',  ['id' => $id]));
});

















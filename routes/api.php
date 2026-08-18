<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiAppController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Middleware\VerifyHubToken;
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

Route::post('/login',[ApiAppController::class, 'login']);      
Route::post('/login-with-code',[ApiAppController::class, 'loginCode']);
Route::post('/login/employee',[ApiAppController::class, 'login']); // Route alternative pour employee
Route::post('/verify-user-type',[ApiAppController::class, 'verifyUserType']);
Route::post('/check-user-type',[ApiAppController::class, 'checkUserType']);

// Routes Hub – accès via proxy FlowHub (protégées par X-Hub-Token, pas JWT)
Route::middleware(['verify.hub.token'])->group(function () {
    Route::get('/dashboard/kpis', [\App\Http\Controllers\Api\DashboardApiController::class, 'getKpis']);
    Route::get('/dashboard/exercices', [\App\Http\Controllers\Api\DashboardApiController::class, 'getExercices']);
});

Route::middleware(['jwt.auth'])->group(function () {

    //Employee Routes
    Route::get('/logout', [ApiAppController::class, 'logout']);

    Route::post('/changePassword/{id}', [ApiAppController::class, 'changePassword']);
    Route::post('/forgot-password/{email}', [ApiAppController::class, 'forgotPassword']);

    Route::get('/userData', [ApiAppController::class, 'userData']);
    Route::get('/empData', [ApiAppController::class, 'empData']);
    Route::post('/empUpdate/{id}', [ApiAppController::class, 'empUpdate']);

    Route::get('/dashboardCompany/{id}', [ApiAppController::class, 'dashboardCompany']);

    Route::get('/profile', [ApiAppController::class, 'profileEmployee']);
    Route::get('/getDossiersEmp/{id}', [ApiAppController::class, 'getDossiersEmp']);
    Route::get('/getPayslips/{id}', [ApiAppController::class, 'getPayslips']);
    Route::get('/getPayslipsLive', [ApiAppController::class, 'getPayslipsLive']);
    Route::get('/documents', [ApiAppController::class, 'documentsEmployee']);
    Route::get('/documents/{id}/download', [ApiAppController::class, 'downloadDocument']);
    Route::delete('/documents/{id}', [ApiAppController::class, 'deleteDocument']);

    Route::get('/timesheet', [ApiAppController::class, 'indexTimesheet']);
    Route::post('timesheet/store', [ApiAppController::class, 'storeTimeSheet']);
    Route::get('/getShowPointage', [ApiAppController::class, 'getShowPointage']);
    Route::get('/getPointageLive', [ApiAppController::class, 'getPointageLive']);
    Route::get('/timesheet/{id?}/edit', [ApiAppController::class, 'edit']);
    Route::put('/timesheet/{id}', [ApiAppController::class, 'update']);
    Route::delete('/timesheet/{id?}', [ApiAppController::class, 'destroy']);
    Route::get('/CountTimesheetApprv/', [ApiAppController::class, 'CountTimesheetApprv']);
    Route::get('/CountTimesheetPend/', [ApiAppController::class, 'CountTimesheetPend']);
    Route::get('/CountTimesheetRejc/', [ApiAppController::class, 'CountTimesheetRejc']);
    Route::get('/CountTimesheetEmp/{id}', [ApiAppController::class, 'CountTimesheetEmp']);
    Route::get('/CountHeuresAbsence', [ApiAppController::class, 'CountHeuresAbsence']);
    Route::get('/CountLoans', [ApiAppController::class, 'CountLoans']);

    Route::get('/leave', [ApiAppController::class, 'indexleave']);
    Route::get('leave/show/{id}', [ApiAppController::class, 'show']);
    Route::get('leave/{id?}/edit', [ApiAppController::class, 'edit']);
    Route::put('leave/{id}', [ApiAppController::class, 'update']);
    Route::delete('leave/{id?}', [ApiAppController::class, 'destroy']);
    Route::get('/CountleaveApprv/', [ApiAppController::class, 'CountleaveApprv']);
    Route::get('/CountleavePend/', [ApiAppController::class, 'CountleavePend']);
    Route::get('/CountleaveRejc/', [ApiAppController::class, 'CountleaveRejc']);
    Route::get('/CountleaveEmp/{id}', [ApiAppController::class, 'CountleaveEmp']);
    Route::get('/getMonthTrait',[ApiAppController::class, 'MonthTrait']);
    Route::post('/leave/create', [ApiAppController::class, 'createLeaveRequest']);
    Route::get('/leave/types', [ApiAppController::class, 'getLeaveTypes']);
    Route::get('/leave/history', [ApiAppController::class, 'getEmployeeLeaveHistory']);
    Route::get('/demandes', [ApiAppController::class, 'demandeRequest']);
    Route::get('/announcements-events', [ApiAppController::class, 'indexAnnoucements']);
    Route::post('/pointage/scanner', [ApiAppController::class, 'scannerPointage']);
    Route::get('payslip/GeneratePdf/{id}/{monthpaie?}', [ApiAppController::class, 'generatePdf'])->where('monthpaie', '.*');
    Route::get('payslip/viewBulletin/{id}/{monthpaie?}', [ApiAppController::class, 'viewBulletin']);
    Route::get('/notifications/unread-count-emp', [ApiAppController::class, 'getUnreadCountEmp']);
    Route::post('/notifications/mark-all-as-read-emp', [ApiAppController::class, 'markAllAsReadEmp']);
    Route::post('timesheet/store-base64', [ApiAppController::class, 'storeTimeSheetBase64']);
    Route::post('document/store-base64',[ApiAppController::class, 'storeDocumentBase64']);
    //Route::get('/user/loginAs/{id}', [ApiAppController::class, 'loginAs']);
    //Route::get('/user/redirectWithToken/{token}', [ApiAppController::class, 'redirectWithToken']);

    /*-----------------------------------------------------------------------------------------------------------------------------------------------------------
    //Entreprise Routes
    -----------------------------------------------------------------------------------------------------------------------------------------------------------*/

    Route::post('/changePasswordCompany/{id}', [ApiAppController::class, 'changePasswordCompany']);
    //Route::post('/empUpdateAvatar/{id}', [ApiAppController::class, 'empUpdateAvatar']);
    Route::post('/companyUpdate/{id}', [ApiAppController::class, 'companyUpdate']);

    Route::get('/showAvatarUpdateForm', [ApiAppController::class, 'showAvatarUpdateForm'])->name('show.update.avatar.form');
    Route::post('/empUpdateAvatar', [ApiAppController::class, 'empUpdateAvatar'])->name('emp.update.avatar');

    Route::get('/getPayslipsCompany', [ApiAppController::class, 'getPayslipsCompany']);
    Route::get('/getlastPayslipsCompany', [ApiAppController::class, 'getlastPayslipsCompany']);
    Route::get('/monthTraitCompany', [ApiAppController::class, 'MonthTraitCompany']);
    Route::get('/announcementsEvents', [ApiAppController::class, 'announcementsEvents']);
    Route::get('/demandeRequestCompany', [ApiAppController::class, 'demandeRequestCompany']);
    Route::get('/getMassNetSalary', [ApiAppController::class, 'getMassNetSalary']);
    Route::get('/getEmployeesInfo', [ApiAppController::class, 'getEmployeesInfo']);
    Route::get('/getShowPointageCompany/{id}', [ApiAppController::class, 'getShowPointageCompany']);
    Route::post('/updateRequestStatus/{id}', [ApiAppController::class, 'updateRequestStatus']);
    Route::get('/companyData', [ApiAppController::class, 'companyData']);
    Route::get('/getDossiersCompany/{id}', [ApiAppController::class, 'getDossiersCompany']);

    Route::get('/profilCompany', [ApiAppController::class, 'profilCompany']);
    Route::get('/pointages/{date}', [ApiAppController::class, 'getPointagesByDate']);
    Route::post('/pointages/employee/{employee_id}', [ApiAppController::class, 'createPointage']);
    Route::get('/employeesRewards', [ApiAppController::class, 'getEmployeesRewards']);
    Route::get('/typeRewards', [ApiAppController::class, 'getTypeRewards']);
    Route::post('/rewardEmployee/{employee_id}/{awardtype_id}', [ApiAppController::class, 'createEmployeeRewards']);

    Route::get('/getCotisationCompany/{date}', [ApiAppController::class, 'getCotisationCompany']);

    Route::get('/notifCompany', [ApiAppController::class, 'notifCompany']);
    Route::get('/notifEmployee', [ApiAppController::class, 'notifEmployee']);


    Route::get('/notifications/unread-count-com', [ApiAppController::class, 'getUnreadCountCom']);

    Route::post('/notifications/{id}/read', [ApiAppController::class, 'markAsRead']);

    Route::post('/notifications/mark-all-as-read-com', [ApiAppController::class, 'markAllAsReadCom']);
    Route::post('/notifications', [ApiAppController::class, 'storeNotification']);

    Route::post('/user/avatar', [ApiAppController::class, 'updateAvatar']);
    Route::post('/upload', [ApiAppController::class, 'upload']);


    Route::post('/generate-temporary-token', [ApiAppController::class, 'generateTemporaryToken'])
    ->name('generate.temporary.token');

    Route::post('/profile/update-avatar', [ApiAppController::class, 'updateTheAvatar'])->name('update.account');
    
   
});
Route::middleware([VerifyHubToken::class])->group(function () {
    Route::get('/companies', [DashboardApiController::class, 'getCompanies']);
    Route::get('/dashboard/kpis', [DashboardApiController::class, 'getKpis']);
});
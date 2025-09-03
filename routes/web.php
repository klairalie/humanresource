<?php

use App\Http\Controllers\ArchivedprofilesController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\EmployeeprofilesController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EvaluateservicesController;
use App\Models\Employeeprofiles;
use App\Models\Archiveprofile;
use App\Models\Deduction;
use Illuminate\Support\Facades\Route;

Route::controller(DashboardController::class)->group(function () {
Route::get('/HR', 'dashboard')->name('show.dashboard');
});

Route::controller(AttendanceController::class)->group(function () {
    Route::get('attendance', 'showAttendance')->name('show.attendance'); 
    Route::get('/list_overtime', 'showOvertime')->name('show.overtime');
    Route::get('/manage_leave', 'showLeaverequest')->name('show.leaverequest');
    Route::get('/attendanceform', 'showAttendanceform')->name('show.attendanceform');
    Route::post('/attendanceform', 'submitAttendance')->name('submit.attendanceform');
});

Route::controller(EmployeeprofilesController::class)->group(function () {
    Route::get('/Employeeprofiles', 'showEmployeeprofiles')->name('show.employeeprofiles');
    Route::get('HR/employeeprofiles', 'EmployeeprofilesForm')->name('emp.form');
    Route::post('/HR/Employeeprofiles', 'submitEmployeeprofiles')->name('submit.employeeprofiles');
    Route::get('/profileupdate/{employeeprofiles_id}', 'edit')->name('show.edit');
    Route::put('/profileupdate/{employeeprofiles_id}', 'update')->name('update.profile');
    Route::post('/Employeeprofiles/{employeeprofiles_id}/deactivate', 'deactivate')->name('employee.deactivate');
    
});

Route::controller(PayrollController::class)->group( function(){

    Route::get('/payrollform', 'showPayrollform')->name('show.payrollform');
    Route::post('/payrollform', 'createPayroll')->name('create.payroll');
    Route::get('/view_payroll', 'viewpayroll')->name('view.payroll');
    Route::post('/view_payroll/submit-summary', 'submitSummary')->name('payroll.submitSummary');


});

Route::controller(EvaluateservicesController::class)->group(function(){

    Route::get('/evaluateservices', 'showEvaluateServices')->name('show.evaluateservices');
    Route::get('/quoation', 'showQuotationForm')->name('show.quotationform');
});
 
Route::controller(ArchivedprofilesController::class)->group(function(){
Route::get('/archivedprofiles/login', 'loginForm')->name('archived.login');
Route::post('/archivedprofiles/login', 'login')->name('archived.login.submit');
Route::get('/archivedprofiles', 'showArchivedProfiles')->name('archived.profiles');
Route::get('/archivedprofiles/logout', 'logout')->name('archived.logout');
Route::put('/archivedprofiles/{archiveprofile_id}/reactivate', 'reactivate')->name('archived.reactivate');

});

Route::controller(DeductionController::class)->group(function () {
    Route::get('/deductionform', 'showDeductionForm')->name('show.deductionform');
    Route::get('/deductions/create', 'createDeduction')->name('create.deduction');
    Route::post('/deductions', 'store')->name('deductions.store');
});
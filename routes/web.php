<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeprofilesController;
use App\Http\Controllers\PayrollController;
use App\Models\Employeeprofiles;
use Illuminate\Support\Facades\Route;

Route::get('/HR', [DashboardController::class, 'dashboard'])->name('show.dashboard');

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
    Route::get('/profileupdate/{employeeprofile_id}', 'edit')->name('show.edit');
    Route::put('/profileupdate/{employeeprofile_id}', 'update')->name('update.profile');
});

Route::controller(PayrollController::class)->group( function(){

    Route::get('/payrollform', 'showPayrollform')->name('show.payrollform');
    Route::post('/payrollform', 'createPayroll')->name('create.payroll');
    Route::get('/view_payroll', 'viewpayroll')->name('view.payroll');
    Route::post('/view_payroll', 'payrollcompute')->name('payroll.compute');
    

});
 
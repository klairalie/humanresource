<?php

use App\Http\Controllers\ArchivedprofilesController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeprofilesController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EvaluateservicesController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\QueueMonitorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentTokenController;
use App\Http\Controllers\AssessmentResultController;
use App\Http\Controllers\AssessmentQuestionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\EvaluationQuestionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthTransferController;
use App\Http\Middleware\CheckAuth;
use App\Http\Controllers\ProfileController;
 use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AnnouncementController;
//COMMENTED ROUTES ARE NOT ALREADY USED

Route::get('/auth/verify', [AuthTransferController::class, 'verify'])->name('auth.verify');


Route::middleware(['web', CheckAuth::class])->group(function () {

   

Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
Route::get('/holidays/update', [HolidayController::class, 'updateHolidays'])->name('holidays.update');

Route::controller(DashboardController::class)->group(function () {
    Route::get('/HR', 'dashboard')->name('show.dashboard');
    
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/settings', 'showSettings')->name('settings.index');
    Route::get('/attendance/export', 'exportAttendance')->name('attendance.export');
    Route::get('/dashboard/activities', 'recentActivities')->name('dashboard.activities');
    Route::get('/export-services', 'exportServices')->name('services.export');
});

Route::post('/payroll/apply-bonus', [PayrollController::class, 'applyBonusToPresent']);

Route::get('/create-daily-attendance', [EmployeeAttendanceController::class, 'createDailyAttendance']);

Route::get('/recent-activities', [ActivityLogController::class, 'index'])->name('recent-activities.index');

Route::controller(AttendanceController::class)->group(function () {
    Route::get('HR/view_attendance', 'showAttendance')->name('show.attendance');
    Route::get('/list_overtime', 'showOvertime')->name('show.overtime');
    Route::get('/manage_leave', 'showLeaverequest')->name('show.leaverequest');
    Route::get('/attendanceform', 'showAttendanceform')->name('show.attendanceform');
    Route::post('/attendanceform', 'submitAttendance')->name('submit.attendanceform');
      Route::post('/leaverequests/{id}/approve', 'approve')->name('leave.approve');
    Route::post('/leaverequests/{id}/reject', 'reject')->name('leave.reject');
      Route::post('/overtime-requests/{id}/approve', 'approveOvertime')->name('overtime.approve');
    Route::post('/overtime-requests/{id}/reject', 'rejectOvertime')->name('overtime.reject');
});

Route::controller(EmployeeprofilesController::class)->group(function () {

    // Route::middleware(['check.permission:employeeprofiles_view'])->group(function () {
    Route::get('/Employeeprofiles', 'showEmployeeprofiles')->name('show.employeeprofiles');
    // });
    Route::get('HR/employeeprofiles', 'EmployeeprofilesForm')->name('emp.form');
    Route::post('/HR/Employeeprofiles', 'submitEmployeeprofiles')->name('submit.employeeprofiles');
    Route::get('/profileupdate/{employeeprofiles_id}', 'edit')->name('show.edit');
    Route::put('/profileupdate/{employeeprofiles_id}', 'update')->name('update.profile');
    Route::post('/Employeeprofiles/{employeeprofiles_id}/deactivate', 'deactivate')->name('employee.deactivate');
});

Route::controller(PayrollController::class)->group(function () {

    Route::get('/payrollform', 'showPayrollform')->name('show.payrollform');
    Route::get('/view_payroll', 'viewPayroll')->name('view.payroll');
   Route::post('/payroll/store', [PayrollController::class, 'storePayroll'])->name('store.payroll');
    Route::get('/payroll/records/{employeeprofiles_id}', [PayrollController::class, 'getEmployeePayroll'])
    ->name('employee.payroll.records');

});

Route::controller(EvaluateservicesController::class)->group(function () {
    Route::get('/evaluateservices', 'showEvaluateServices')->name('show.evaluateservices');
    Route::post('/service/update-status/{id}', 'updateStatus')->name('service.update-status');
    Route::get('/service/details/{id}', 'getServiceDetails')->name('service.details'); // ✅ Added this
});
Route::controller(ArchivedprofilesController::class)->group(function () {
    Route::get('/archivedprofiles/login', 'loginForm')->name('archived.login');
    Route::get('/archivedprofiles', 'showArchivedProfiles')->name('archived.profiles');
    Route::put('/archivedprofiles/{archiveprofile_id}/reactivate', 'reactivate')->name('archived.reactivate');
});


Route::get('/results', [AssessmentResultController::class, 'showEvaluationResults'])->name('results.index');

Route::controller(ApplicantController::class)->group(function () {
    Route::post('/applicationform', 'store')->name('applicants.store');
    Route::get('/applicationform', 'showForm')->name('show.applicationform');
    Route::get('/applicants', 'index')->name('show.listapplicants');
    Route::get('/applicants/{applicant_id}', 'show')->name('applicants.show');
    Route::post('/applicants/validate', 'validateField')->name('validate.applicant');
    Route::post('/applicants/{applicant_id}/summarize', 'summarize')->name('applicants.summarize');
    Route::get('/applicants/summary/{applicant_summary_id}', 'showSummary')->name('applicants.summary.show');
    Route::get('/applicants/{applicant_id}/review', 'review')->name('review.document');
    Route::post('/applicants/{applicant_id}/reviewed', 'markReviewed')->name('applicant.markReviewed');
    Route::get('/summary/{applicant_id}/resume/view', 'viewResume')->name('applicant.resume.view');
    Route::post('/applicants/{applicant_id}/mark-screening', 'markScreening')
        ->name('applicant.markScreening');
    Route::post('/applicants/{id}/pass', 'markPassed')->name('applicants.pass');
    Route::post('/applicants/{id}/fail', 'markFailed')->name('applicants.fail');
    Route::post('/applicants/{applicant_id}/schedule-interview', 'markScheduledInterview')
        ->name('applicant.scheduleInterview');
    Route::delete('/applicants/{id}/delete', 'deleteApplicant')->name('applicants.delete');
    Route::put('/applicants/{applicant}/status/{status}', 'updateStatus')->name('applicants.updateStatus');
});

Route::get('/resumeupload', [ResumeController::class, 'showResumeForm'])->name('resume.form');
Route::post('/resumeupload', [ResumeController::class, 'upload'])->name('resume.upload');
Route::delete('/resumeupload/{resume_format_id}', [ResumeController::class, 'delete'])->name('resume.delete');
Route::get('/resumeupload/{resume_format_id}/details', [ResumeController::class, 'details'])->name('resume.details');
Route::get('/resumeupload/download', [ResumeController::class, 'download'])->name('resume.download');


Route::get('/queue/failures', [QueueMonitorController::class, 'index'])->name('queue.failures');
Route::post('/queue/retry/{id}', [QueueMonitorController::class, 'retryJob'])->name('queue.retry');
Route::post('/queue/retry-all', [QueueMonitorController::class, 'retryAll'])->name('queue.retryAll');
Route::delete('/queue/delete/{id}', [QueueMonitorController::class, 'deleteJob'])->name('queue.delete');
Route::delete('/queue/clear-all', [QueueMonitorController::class, 'clearAll'])->name('queue.clearAll');




// HR/Staff view applicant results

Route::controller(AssessmentTokenController::class)->group(function () {
    Route::post('/assessment/{applicant_id}/{assessment_id}', 'sendAssessment')->name('send.assessment');
    Route::post('/applicants/{applicant_id}/send-assessment', 'send')->name('assessment.send');
});



Route::controller(InterviewController::class)->group(function () {

    Route::put('/interviews/{applicant}/{status}', 'updateStatus')->name('interviews.updateStatus');
    Route::put('/applicants/{applicant}/{status}', 'finalDecision')->name('applicants.finalDecision');
});



Route::controller(AssessmentResultController::class)->group(function () {
    Route::post('/assessment/submit/{token}', [AssessmentResultController::class, 'store'])->name('assessment.submit');
    Route::get('/assessmentresult', [AssessmentResultController::class, 'showAssessmentResults'])->name('assessment.results');
    Route::get('/assessment-results/{applicant_id}', [AssessmentResultController::class, 'showResult'])->name('assessment.result.view');


Route::get('/results', [AssessmentResultController::class, 'showEvaluationResults'])->name('results.index');


});


Route::controller(EmployeeAttendanceController::class)->group(function () {
    Route::get('/EmpAttendance/Attendancepage', 'showEmpAttendance')->name('employee.attendance');
    Route::get('/api/get-employee/{cardNumber}', 'getEmployeeByCard');
    Route::post('/attendance/verify-otp', 'verifyOtp')->name('attendance.verifyOtp');
});




Route::controller(EvaluationQuestionController::class)->group(function () {

    // List all evaluation questions
    Route::get('/evaluation/questions', 'showEvaluation')->name('evaluation.view');

    // Show form to create a new question
    Route::get('/evaluation/questions/create', 'create')->name('evaluation.create');

    // Store a new evaluation question
    Route::post('/evaluation/questions', 'store')->name('evaluation.store');

    // Bulk delete & edit
    Route::delete('/evaluation/deleteAll', 'deleteAll')->name('evaluation.bulkDelete');
    Route::put('/evaluation/{id}', 'update')->name('evaluation.update');

    // Show HR send page
    Route::get('/evaluation/send', 'sendEvalView')->name('evaluation.send.view');

    // Process send form
    Route::post('/evaluation/send', 'sendEvaluation')->name('evaluation.send');

    // Token-based access to questionnaire
    Route::get('/evaluation/questionnaire/{token}', 'showEvaluationQuestionnaire')->name('evaluation.questionnaire');

    Route::post('/evaluation/submitEvaluation/{token}', [EvaluationQuestionController::class, 'submitEvaluation'])
        ->name('evaluation.submit');

    Route::get('/evaluate/expired', 'showExpired')->name('evaluate.expired');
    Route::get('/evaluate/used', 'showUsed')->name('evaluate.used');
    Route::get('/evaluate/thankyou', 'showThankYou')->name('evaluate.thankyou');
    Route::get('/evaluate/alreadydone', 'showAlreadyDone')->name('evaluate.alreadydone');

    Route::get('/service/details/{id}', [ServiceController::class, 'showDetails']);
});

Route::controller(BookingController::class)->group(function () {

    Route::get('/Booking', 'index')->name('show.bookingindex');
});

Route::get('/editprofile', [ProfileController::class, 'showEditProfile'])->name('show.editprofile');
Route::put('/editprofile', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/notifications/mark-all-read', function () {
    session()->forget('notifications'); // optional if stored in session
    cache()->forget('notifications');   // optional if stored in cache
    return response()->json(['success' => true]);
})->name('notifications.markAllRead');

Route::get('/attendance/details/ajax/{employeeprofiles_id}', [AttendanceController::class, 'getEmployeeDetails'])
    ->name('attendance.details.ajax');

});

Route::controller(AssessmentQuestionController::class)->group(function () {
    Route::get('/AssessmentQuestions/viewquestions', 'assessmentView')->name('view.questions');
    Route::get('/AssessmentQuestions/create', 'create')->name('Questions.create');
    Route::post('/AssessmentQuestions/store', 'store')->name('Questions.store');
    Route::delete('/AssessmentQuestions/destroy-all', 'destroyAll')->name('Questions.destroyAll');
    Route::get('AssessmentQuestions/edit', 'edit')->name('Questions.edit');
    Route::put('AssessmentQuestions/update/{assessmentQuestion}', 'update')->name('Questions.update');
});

Route::controller(AssessmentController::class)->group(function () {
    Route::get('/assessments/create', 'create')->name('assessments.create');
    Route::post('/assessments/store', 'store')->name('assessments.store');
    Route::post('/assessment/begin', 'begin')->name('assessment.begin');

    // Change showStartPage → showQuestionnaire
    Route::get('/assessment/start/{token}', 'showQuestionnaire')->name('assessment.start');

    Route::get('/assessment/questionnaire/{token}', 'showQuestionnaire')->name('assessment.questionnaire');
    Route::post('/Assessment/assessmentquestionnaire/{token}', 'storeQuestion')->name('question.store');
    Route::get('/assessment/result/{token}', 'showResult')->name('assessment.result');
});

Route::post('/attendance/manual-update', [EmployeeAttendanceController::class, 'manualUpdate'])->name('attendance.manualUpdate');

Route::controller(EmployeeAttendanceController::class)->group(function () {
    Route::get('/Attendancepage', 'showEmpAttendance')->name('employee.attendance');
});
Route::get('/attendance/employees', [EmployeeAttendanceController::class, 'getEmployees']);
Route::get('/attendance/descriptor/{id}', [EmployeeAttendanceController::class, 'getDescriptor']);
Route::post('/attendance/record', [EmployeeAttendanceController::class, 'recordAttendance']);
Route::get('/attendance/statuses', [AttendanceController::class, 'getStatuses'])->name('attendance.statuses');
Route::post('/attendance/admin-update', [EmployeeAttendanceController::class, 'adminUpdate'])->name('attendance.adminUpdate');
// web.php
Route::post('/check-face-duplicate', [EmployeeprofilesController::class, 'checkFaceDuplicate'])
    ->name('check-face-duplicate');

    // Payroll Routes
Route::get('/payrolls', [PayrollController::class, 'payrollRecord'])
    ->name('payroll.records');

Route::get('/payroll/filter/{id}', [PayrollController::class, 'filterRecords'])
    ->name('payroll.filter');

Route::get('/payroll/export/excel/{employee}', [PayrollController::class, 'excel'])
    ->name('payroll.export.excel');

Route::get('/payroll/export/pdf/{employee}', [PayrollController::class, 'pdf'])
    ->name('payroll.export.pdf');

Route::get('/payroll/print/{employee}', [PayrollController::class, 'print'])
    ->name('payroll.print');

Route::get('/payroll/print/company', [PayrollController::class, 'printCompany'])
    ->name('payroll.print.company');


// get all descriptors (used by frontend)
Route::get('/attendance/descriptors', [EmployeeAttendanceController::class, 'getAllDescriptors']);


Route::get('/announcements',  [AnnouncementController::class, 'index'])->name('announcements.index');
Route::post('/announcements',  [AnnouncementController::class, 'store'])->name('announcements.store');
Route::put('/announcements/{id}',  [AnnouncementController::class, 'update'])->name('announcements.update');
Route::delete('/announcements/{id}',  [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
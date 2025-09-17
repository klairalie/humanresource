<?php

use App\Http\Controllers\ArchivedprofilesController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeductionController;
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
use App\Models\Assessment;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::controller(DashboardController::class)->group(function () {
    Route::get('/HR', 'dashboard')->name('show.dashboard');
    Route::get('/editprofile', 'showEditProfile')->name('show.editprofile');
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

Route::controller(PayrollController::class)->group(function () {

    Route::get('/payrollform', 'showPayrollform')->name('show.payrollform');
    Route::post('/payrollform', 'createPayroll')->name('create.payroll');
    Route::get('/view_payroll', 'viewpayroll')->name('view.payroll');
    Route::post('/view_payroll/submit-summary', 'submitSummary')->name('payroll.submitSummary');
    Route::put('/payroll/store', 'storePayroll')->name('store.payroll');
});

Route::controller(EvaluateservicesController::class)->group(function () {

    Route::get('/evaluateservices', 'showEvaluateServices')->name('show.evaluateservices');
    Route::get('/quoation', 'showQuotationForm')->name('show.quotationform');
});

Route::controller(ArchivedprofilesController::class)->group(function () {
    Route::get('/archivedprofiles/login', 'loginForm')->name('archived.login');
    Route::post('/archivedprofiles/login', 'login')->name('archived.login.submit');
    Route::get('/archivedprofiles', 'showArchivedProfiles')->name('archived.profiles');
    Route::get('/archivedprofiles/logout', 'logout')->name('archived.logout');
    Route::put('/archivedprofiles/{archiveprofile_id}/reactivate', 'reactivate')->name('archived.reactivate');
});

Route::controller(DeductionController::class)->group(function () {
    Route::get('/deductionform', 'showDeductionForm')->name('show.deductionform');
    Route::post('/deductionform', 'storeDeduction')->name('store.deduction');
    Route::get('/deductionrecords', 'viewDeductionRecords')->name('view.deductionrecords');
    Route::get('/deductionrecords/{deduction_id}/edit', 'editDeduction')->name('edit.deduction');
    Route::put('/deductionrecords/{deduction_id}/update', 'updateDeduction')->name('update.deduction');
    Route::delete('/deductionrecords/{deduction_id}/delete', 'deleteDeduction')->name('delete.deduction');
});

Route::get('/payroll/records/{employeeprofiles_id}', [PayrollController::class, 'getEmployeePayroll'])
    ->name('employee.payroll.records');


Route::controller(ApplicantController::class)->group(function () {
    Route::post('/applicationform', 'store')->name('applicants.store');
    Route::get('/applicationform', 'showForm')->name('show.applicationform');
    Route::get('/applicants', 'index')->name('show.listapplicants');
    Route::get('/applicants/{applicant_id}', 'show')->name('applicants.show');
    Route::post('/applicants/validate', 'validateField')->name('validate.applicant');
    Route::post('/applicants/{applicant_id}/summarize', 'summarize')->name('applicants.summarize');
    Route::get('/summary/{applicant_summary_id}', 'showSummary')->name('applicants.summary.show');
    Route::get('/applicants/{applicant_id}/review', 'review')->name('review.document');
    Route::post('/applicants/{applicant_id}/reviewed', 'markReviewed')->name('applicant.markReviewed');
    Route::get('/summary/{applicant_id}/resume/view', 'viewResume')->name('applicant.resume.view');
});

Route::get('/resumeupload', [ResumeController::class, 'showResumeForm'])->name('resume.form');
Route::post('/resumeupload', [ResumeController::class, 'upload'])->name('resume.upload');
Route::delete('/resumeupload/{resume_format_id}', [ResumeController::class, 'delete'])->name('resume.delete');
Route::get('/resumeupload/{resume_format_id}/details', [ResumeController::class, 'details'])->name('resume.details');
Route::get('/resumeupload/download', [ResumeController::class, 'download'])->name('resume.download');


Route::get('/queue-failures', [QueueMonitorController::class, 'index'])->name('queue.failures');

// Assessments (CRUD for admin/HR)
// Route::prefix('assessmentque')->group(function () {
//     Route::get('/', [AssessmentController::class, 'index'])->name('assessments.index');
//     Route::get('/create', [AssessmentController::class, 'create'])->name('assessments.create');
//     Route::post('/', [AssessmentController::class, 'store'])->name('assessments.store');
//     Route::get('/{assessment_id}', [AssessmentController::class, 'show'])->name('assessments.show');
//     Route::get('/{assessment_id}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
//     Route::put('/{assessment_id}', [AssessmentController::class, 'update'])->name('assessments.update');
//     Route::delete('/{assessment_id}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
// });

// Tokens (Send assessment links to applicants)
Route::post('/applicants/{applicant_id}/send-assessment', [AssessmentTokenController::class, 'send'])
    ->name('assessment.send');

// Public route for applicants to open their assessment (via token)

// Submit results
Route::post('/assessment/submit/{token}', [AssessmentResultController::class, 'store'])
    ->name('assessment.submit');

// HR/Staff view applicant results

Route::controller(AssessmentTokenController::class)->group(function () {

    Route::post('/assessment/{applicant_id}/{assessment_id}', 'sendAssessment')->name('send.assessment');
    Route::get('/assessment/results/{applicant_id}', 'show')->name('assessment.results.show');
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

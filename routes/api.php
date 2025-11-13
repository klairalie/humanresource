<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employeeprofiles;

Route::get('/get-employee/{cardNumber}', function ($cardNumber) {
    $employee = Employeeprofiles::where('card_Idnumber', $cardNumber)->first();

    if ($employee) {
        return response()->json([
            'success' => true,
            'employee' => [
                'employeeprofiles_id' => $employee->employeeprofiles_id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'position' => $employee->position,
                'email' => $employee->email,
            ],
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Card not found or not registered.'
    ]);
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Leaves\Http\Controllers\LeavesController;

// Routes pour les entreprises
Route::middleware(['auth', 'maintenance'])->prefix('company')->name('company.')->group(function () {
    //Leaves
    Route::get('leaves/index', [LeavesController::class, 'index'])->name('leaves.index');
    Route::get('leaves/create', [LeavesController::class, 'create'])->name('leaves.create');
    Route::post('leaves/store', [LeavesController::class, 'store'])->name('leaves.store');
    Route::get('leaves/show/{id}', [LeavesController::class, 'show'])->name('leaves.show');
    Route::get('leaves/edit/{id}', [LeavesController::class, 'edit'])->name('leaves.edit');
    Route::put('leaves/update/{id}', [LeavesController::class, 'update'])->name('leaves.update');
    Route::delete('leaves/destroy/{id}', [LeavesController::class, 'destroy'])->name('leaves.destroy');
    Route::get('leaves/calendar', [LeavesController::class, 'calendar'])->name('leaves.calendar');
    Route::get('leaves/get_employee_leave_date', [LeavesController::class, 'getEmployeeLeaveDate'])->name('leaves.get_employee_leave_date');
    Route::get('leaves/get_employee_leave_sb', [LeavesController::class, 'getEmployeeLeaveSB'])->name('leaves.get_employee_leave_sb');
    Route::get('leaves/attestation/{id}', [LeavesController::class, 'attestationForm'])->name('leaves.attestation');
    Route::get('leaves/datasLeave', [LeavesController::class, 'datasLeave'])->name('leaves.datasLeave');
    Route::post('leaves/changeaction/{id}', [LeavesController::class, 'changeStatus'])->name('leaves.changeaction');
    Route::get('leaves/activate-form/{id}', [LeavesController::class, 'activateForm'])->name('leaves.activateForm');
    Route::post('leaves/activate/{id}', [LeavesController::class, 'activate'])->name('leaves.activate');
    Route::post('leaves/deactivate/{id}', [LeavesController::class, 'deactivate'])->name('leaves.deactivate');
    Route::get('leaves/start/{id}', [LeavesController::class, 'startLeave'])->name('leaves.start');
    Route::get('leaves/end/{id}', [LeavesController::class, 'endLeave'])->name('leaves.end');
});

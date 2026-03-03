<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Employee\EventBrowseController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\Admin\AttendanceController;

// Ping route
Route::get('/ping', function () {
    return 'PONG';
});

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Admin Event routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Event CRUD + actions
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/start', [EventController::class, 'start'])->name('events.start');
    Route::post('/events/{event}/end', [EventController::class, 'end'])->name('events.end');
    Route::post('/events/{event}/generate-batches', [EventController::class, 'generateBatches'])->name('events.generateBatches');
    Route::post('/events/{event}/auto-mode', [EventController::class, 'toggleAutoMode'])->name('events.autoMode');
    Route::post('/events/{event}/reset', [EventController::class, 'resetDemo'])->name('events.reset');

    // edit/update
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');

    // delete
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Attendance routes
    Route::get('/events/{event}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/events/{event}/attendance/scan', [AttendanceController::class, 'scanCheckIn'])->name('attendance.scan');
    Route::post('/events/{event}/attendance/{registration}/checkin', [AttendanceController::class, 'checkIn'])
    ->name('attendance.checkin');
    Route::post('/events/{event}/attendance/{registration}/undo', [AttendanceController::class, 'undoCheckIn'])->name('attendance.undo');

});

// Admin start batch
Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('/batches/{batch}/start', [BatchController::class, 'start'])->name('batches.start');
    Route::post('/batches/{batch}/complete', [BatchController::class, 'complete'])->name('batches.complete'); // optional
});

// Display fullscreen + data JSON
Route::get('/display/event/{event}', [DisplayController::class, 'show'  ])->name('display.event');
Route::get('/display/event/{event}/data', [DisplayController::class, 'data'])->name('display.event.data');

// Employee routes
Route::get('/events', [EventBrowseController::class, 'index'])->name('employee.events.index');
Route::get('/events/{event}', [EventBrowseController::class, 'show'])->name('employee.events.show');

// Registration routes
Route::get('/events/{event}/batches/{batch}', [EventBrowseController::class, 'registerForm'])
    ->name('employee.events.registerForm');
Route::post('/events/{event}/batches/{batch}/register', [EventBrowseController::class, 'registerSubmit'])
    ->name('employee.events.registerSubmit');

// Ticket login routes
Route::get('/ticket/login', [EventBrowseController::class, 'ticketLoginForm'])->name('employee.ticket.loginForm');
Route::post('/ticket/login', [EventBrowseController::class, 'ticketLoginSubmit'])->name('employee.ticket.loginSubmit');

// Ticket view routes
Route::get('/ticket/{registration}', [EventBrowseController::class, 'ticket'])
    ->name('employee.ticket');
Route::get('/ticket/{registration}/live', [DisplayController::class, 'ticketLive'])
  ->name('employee.ticket.live');








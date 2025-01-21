<?php
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowRecordController;
use Illuminate\Support\Facades\Auth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books', [BookController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/{book_id}/borrow', [BorrowRecordController::class, 'borrow']);
    Route::post('/borrow-records/{record_id}/pay-fine', [BorrowRecordController::class, 'payFine']);
    Route::get('/calculate_fine', [BorrowRecordController::class, 'calculateFines']);
});

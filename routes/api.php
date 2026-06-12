<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\RoomTypesController;
use App\Http\Controllers\FacilitiesController;
use App\Http\Controllers\BookingAddonController;
use App\Http\Controllers\BookingDetailController;
use App\Http\Controllers\HotelFacilitiesController;

// Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);
Route::post('/auth/google', [UserController::class, 'googleLogin']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    // ----------------------------------------------------------------
    // --- ROUTE UNTUK UPDATE PROFILE ---
    // ----------------------------------------------------------------
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/privacy', [UserController::class, 'updatePrivacy']);

    // ----------------------------------------------------------------
    // Bookings — route statis WAJIB di atas /{id}
    // ----------------------------------------------------------------
    Route::prefix('bookings')->group(function () {
        Route::get('/',       [BookingController::class, 'index']);
        Route::get('/rekap',  [BookingController::class, 'rekap']);  // ✅ dipindah ke atas
        Route::post('/',      [BookingController::class, 'store']);
        Route::get('/{id}',   [BookingController::class, 'show']);
        Route::put('/{id}',   [BookingController::class, 'update']);
        Route::patch('/{id}/status', [BookingController::class, 'updateStatus']);
        Route::delete('/{id}', [BookingController::class, 'destroy']);
    });

    // Booking details — nested dan standalone
    Route::prefix('bookings/{id_booking}/details')->group(function () {
        Route::get('/',    [BookingDetailController::class, 'index']);
        Route::post('/',   [BookingDetailController::class, 'store']);
        Route::get('/{id}',    [BookingDetailController::class, 'show']);
        Route::put('/{id}',    [BookingDetailController::class, 'update']);
        Route::delete('/{id}', [BookingDetailController::class, 'destroy']);
        Route::patch('/{id}/confirm', [BookingDetailController::class, 'confirm']);
        Route::patch('/{id}/cancel',  [BookingDetailController::class, 'cancel']);
    });

    // ✅ Tambah route standalone untuk Flutter (GET /booking-details)
    Route::get('/booking-details', [BookingDetailController::class, 'indexAll']);

    // ----------------------------------------------------------------
    // Hotels — route statis WAJIB di atas /{id}
    // ----------------------------------------------------------------
    Route::prefix('hotels')->group(function () {
        Route::get('/',          [HotelController::class, 'index']);
        Route::get('/kota-list', [HotelController::class, 'kotaList']); // ✅ di atas /{id}
        Route::post('/',         [HotelController::class, 'store']);
        Route::get('/{id}',      [HotelController::class, 'show']);
        Route::post('/{id}',     [HotelController::class, 'update']);
        Route::put('/{id}',      [HotelController::class, 'update']);
        Route::delete('/{id}',   [HotelController::class, 'destroy']);
    });

    Route::get('/hotels/{id_hotel}/reviews/summary', [ReviewController::class, 'summary']);

    Route::prefix('hotels/{id_hotel}/facilities')->group(function () {
        Route::get('/',                [HotelFacilitiesController::class, 'index']);
        Route::post('/',               [HotelFacilitiesController::class, 'store']);
        Route::put('/',                [HotelFacilitiesController::class, 'sync']);
        Route::delete('/',             [HotelFacilitiesController::class, 'destroyAll']);
        Route::delete('/{id_facility}', [HotelFacilitiesController::class, 'destroy']);
    });

    // ----------------------------------------------------------------
    // Rooms — route statis WAJIB di atas /{id}
    // ----------------------------------------------------------------
    Route::prefix('rooms')->group(function () {
        Route::get('/',          [RoomController::class, 'index']);
        Route::get('/available', [RoomController::class, 'available']); // ✅ di atas /{id}
        Route::post('/',         [RoomController::class, 'store']);
        Route::get('/{id}',      [RoomController::class, 'show']);
        Route::put('/{id}',      [RoomController::class, 'update']);
        Route::patch('/{id}',    [RoomController::class, 'update']);
        Route::patch('/{id}/status', [RoomController::class, 'updateStatus']);
        Route::delete('/{id}',   [RoomController::class, 'destroy']);
    });

    // ----------------------------------------------------------------
    // Users
    // ----------------------------------------------------------------
    Route::prefix('users')->group(function () {
        Route::get('/',        [UserController::class, 'index']);
        Route::post('/',       [UserController::class, 'store']);
        Route::get('/{id}',    [UserController::class, 'show']);
        Route::put('/{id}',    [UserController::class, 'update']);
        Route::patch('/{id}',  [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // ----------------------------------------------------------------
    // Lainnya (tidak ada konflik)
    // ----------------------------------------------------------------
    Route::prefix('addons')->group(function () {
        Route::get('/',          [AddonController::class, 'index']);
        Route::get('/available', [AddonController::class, 'available']); // ✅ di atas /{id}
        Route::post('/',         [AddonController::class, 'store']);
        Route::get('/{id}',      [AddonController::class, 'show']);
        Route::put('/{id}',      [AddonController::class, 'update']);
        Route::delete('/{id}',   [AddonController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [AddonController::class, 'toggleStatus']);
    });

    Route::prefix('facilities')->group(function () {
        Route::get('/',        [FacilitiesController::class, 'index']);
        Route::post('/',       [FacilitiesController::class, 'store']);
        Route::get('/{id}',    [FacilitiesController::class, 'show']);
        Route::put('/{id}',    [FacilitiesController::class, 'update']);
        Route::patch('/{id}',  [FacilitiesController::class, 'update']);
        Route::delete('/{id}', [FacilitiesController::class, 'destroy']);
    });

    Route::prefix('room-types')->group(function () {
        Route::get('/',        [RoomTypesController::class, 'index']);
        Route::post('/',       [RoomTypesController::class, 'store']);
        Route::get('/{id}',    [RoomTypesController::class, 'show']);
        Route::put('/{id}',    [RoomTypesController::class, 'update']);
        Route::patch('/{id}',  [RoomTypesController::class, 'update']);
        Route::delete('/{id}', [RoomTypesController::class, 'destroy']);
    });

    Route::prefix('reviews')->group(function () {
        Route::get('/',        [ReviewController::class, 'index']);
        Route::post('/',       [ReviewController::class, 'store']);
        Route::get('/{id}',    [ReviewController::class, 'show']);
        Route::put('/{id}',    [ReviewController::class, 'update']);
        Route::patch('/{id}',  [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/',        [PaymentsController::class, 'index']);
        Route::post('/',       [PaymentsController::class, 'store']);
        Route::get('/{id}',    [PaymentsController::class, 'show']);
        Route::put('/{id}',    [PaymentsController::class, 'update']);
        Route::delete('/{id}', [PaymentsController::class, 'destroy']);
        Route::patch('/{id}/confirm', [PaymentsController::class, 'confirm']);
        Route::patch('/{id}/cancel',  [PaymentsController::class, 'cancel']);
    });

    Route::prefix('booking-addons')->group(function () {
        Route::get('/',    [BookingAddonController::class, 'index']);
        Route::post('/',   [BookingAddonController::class, 'store']);
        Route::get('/{id}',    [BookingAddonController::class, 'show']);
        Route::put('/{id}',    [BookingAddonController::class, 'update']);
        Route::delete('/{id}', [BookingAddonController::class, 'destroy']);
        Route::delete('/booking/{idBooking}', [BookingAddonController::class, 'destroyByBooking']);
    });
});
<?php

use App\Http\Controllers\AuctionsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::redirect('/home', '/');

Route::middleware('guest')->get('/login', [UserController::class, 'login'])->name('login');

Route::middleware('guest')->get('/register', [UserController::class, 'register'])->name('register');

Route::middleware('guest')->get('/forgot-password', [UserController::class, 'forgotPassword'])->name('forgot-password');

Route::middleware('logged')->get('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('logged')->get('/change-password', [UserController::class, 'changePassword'])->name('change-password');

Route::middleware('logged')->get('/transactions', [TransactionsController::class, 'transactions'])->name('transactions');

Route::group(['prefix' => 'auctions', 'middleware' => 'logged'], function() {
    Route::get('/view', [AuctionsController::class, 'myAuctions'])->name('my-auctions');
    Route::get('/create', [AuctionsController::class, 'createAuction'])->name('create-auction');
});

Route::group(['prefix' => 'api'], function() {
    Route::group(['middleware' => 'guest'], function() {
        Route::post('login', [UserController::class, 'API_Login']);
        Route::post('register', [UserController::class, 'API_Register']);
        Route::post('forgot-password/send-code', [UserController::class, 'API_SendCode']);
        Route::post('forgot-password/reset', [UserController::class, 'API_ResetPassword']);
    });
    
    Route::middleware('logged')->post('/change-password', [UserController::class, 'API_ChangePassword']);

    Route::post('auctions', [AuctionsController::class, 'API_GetAuctions']);
    Route::post('auctions/search', [AuctionsController::class, 'API_SearchAuctions']);

    Route::group(['middleware' => 'logged', 'prefix' => 'auctions'], function() {
        Route::post('bidding', [AuctionsController::class, 'API_Bidding']);
        Route::post('my-auctions', [AuctionsController::class, 'API_GetMyAuctions']);
        Route::post('close', [AuctionsController::class, 'API_CloseAuction']);
        Route::post('create', [AuctionsController::class, 'API_CreateAuction']);

    });

    Route::middleware('logged')->post('transaction', [TransactionsController::class, 'API_Transactions']);

});
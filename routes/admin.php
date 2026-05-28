<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UsersViewController;
use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\BonusHistoryController;
use App\Http\Controllers\Admin\OfferwallProviderController;
use App\Http\Controllers\Admin\PayoutMethodController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\PostbackLogController;
use App\Http\Controllers\Admin\FraudLogController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\IntegrationGuideController;
use App\Http\Controllers\Admin\PaymentsController;

Route::middleware(['auth', 'admin'])->group(function () {
    // Overview / Users
    Route::get('/home', [HomeController::class, 'index'])->name('admin.home');
    Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [UsersViewController::class, 'index'])->name('admin.users.view');
    Route::post('/users/{id}', [UsersViewController::class, 'update'])->name('admin.users.update');

    // Offerwall Provider CRUD
    Route::get('/offerwalls',                   [OfferwallProviderController::class, 'index'])->name('admin.offerwalls');
    Route::get('/offerwalls/create',            [OfferwallProviderController::class, 'create'])->name('admin.offerwalls.create');
    Route::post('/offerwalls',                  [OfferwallProviderController::class, 'store'])->name('admin.offerwalls.store');
    Route::get('/offerwalls/{id}/edit',         [OfferwallProviderController::class, 'edit'])->name('admin.offerwalls.edit');
    Route::put('/offerwalls/{id}',              [OfferwallProviderController::class, 'update'])->name('admin.offerwalls.update');
    Route::delete('/offerwalls/{id}',           [OfferwallProviderController::class, 'destroy'])->name('admin.offerwalls.destroy');
    Route::post('/offerwalls/{id}/toggle',      [OfferwallProviderController::class, 'toggle'])->name('admin.offerwalls.toggle');

    // Payout Methods CRUD
    Route::get('/payout-methods',                [PayoutMethodController::class, 'index'])->name('admin.payout-methods');
    Route::get('/payout-methods/create',         [PayoutMethodController::class, 'create'])->name('admin.payout-methods.create');
    Route::post('/payout-methods',               [PayoutMethodController::class, 'store'])->name('admin.payout-methods.store');
    Route::get('/payout-methods/{id}/edit',      [PayoutMethodController::class, 'edit'])->name('admin.payout-methods.edit');
    Route::put('/payout-methods/{id}',           [PayoutMethodController::class, 'update'])->name('admin.payout-methods.update');
    Route::delete('/payout-methods/{id}',        [PayoutMethodController::class, 'destroy'])->name('admin.payout-methods.destroy');
    Route::post('/payout-methods/{id}/toggle',   [PayoutMethodController::class, 'toggle'])->name('admin.payout-methods.toggle');

    // Withdrawals
    Route::get('/withdrawals',                 [WithdrawalController::class, 'index'])->name('admin.withdrawals');
    Route::post('/withdrawals/{id}/approve',   [WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{id}/reject',    [WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
    Route::post('/withdrawals/{id}/mark-paid', [WithdrawalController::class, 'markPaid'])->name('admin.withdrawals.mark-paid');

    // Leads / Postback Logs / Fraud Logs
    Route::get('/leads',          [LeadsController::class, 'index'])->name('admin.leads');
    Route::get('/postback-logs',  [PostbackLogController::class, 'index'])->name('admin.postback-logs');
    Route::get('/fraud-logs',     [FraudLogController::class, 'index'])->name('admin.fraud-logs');

    // Bonuses
    Route::get('/bonus',           [BonusController::class, 'index'])->name('admin.bonus');
    Route::post('/bonus',          [BonusController::class, 'add'])->name('admin.bonus.add');
    Route::put('/bonus',           [BonusController::class, 'update'])->name('admin.bonus.update');
    Route::get('/bonus-history',   [BonusHistoryController::class, 'index'])->name('admin.bonus-history');

    // Settings / Integration Guide
    Route::get('/settings',           [SiteSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings',          [SiteSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/integration-guide',  [IntegrationGuideController::class, 'index'])->name('admin.integration-guide');

    // Legacy payments routes (kept for back-compat; redirect to payout methods)
    Route::get('/payments',                 fn () => redirect()->route('admin.payout-methods'))->name('admin.payments');
    Route::get('/payments/{id}',            [PaymentsController::class, 'view'])->name('admin.payments.view');
    Route::post('/payments/{id}',           [PaymentsController::class, 'update'])->name('admin.payments.update');
    Route::get('/payments/{id}/{price_id}', [PaymentsController::class, 'view'])->name('admin.payments.view.price');
});

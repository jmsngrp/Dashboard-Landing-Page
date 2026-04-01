<?php

use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\AdminBudgetController;
use App\Http\Controllers\Admin\AdminEfficiencyController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminFinancialSnapshotController;
use App\Http\Controllers\Admin\AdminHighlightController;
use App\Http\Controllers\Admin\AdminHighlightKpiController;
use App\Http\Controllers\Admin\AdminImportController;
use App\Http\Controllers\Admin\AdminLocalFundraisingController;
use App\Http\Controllers\Admin\AdminMissionController;
use App\Http\Controllers\Admin\AdminPnlController;
use App\Http\Controllers\Admin\AdminRevenueSharingController;
use App\Http\Controllers\Admin\AdminRevenueSourceController;
use App\Http\Controllers\Admin\AdminAreaAliasController;
use App\Http\Controllers\Admin\AdminGlAccountController;
use App\Http\Controllers\Admin\AdminBucketAmountController;
use App\Http\Controllers\Admin\AdminBudgetBucketController;
use App\Http\Controllers\Admin\AdminDesignController;
use App\Http\Controllers\Admin\AdminGlImportController;
use App\Http\Controllers\Admin\AdminQboController;
use App\Http\Controllers\Admin\AdminStartingCashController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlDrilldownController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin Panel ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('areas', AdminAreaController::class);
    Route::resource('pnl', AdminPnlController::class)->only(['index']);
    Route::resource('mission', AdminMissionController::class);
    Route::resource('efficiency', AdminEfficiencyController::class);
    Route::resource('revenue-sources', AdminRevenueSourceController::class);
    Route::resource('expenses', AdminExpenseController::class);
    Route::resource('budgets', AdminBudgetController::class);
    Route::resource('financial-snapshots', AdminFinancialSnapshotController::class);
    Route::resource('revenue-sharing', AdminRevenueSharingController::class);
    Route::resource('local-fundraising', AdminLocalFundraisingController::class);
    Route::resource('highlights', AdminHighlightController::class);
    Route::resource('highlight-kpis', AdminHighlightKpiController::class);
    Route::get('import', [AdminImportController::class, 'index'])->name('import.index');
    Route::post('import', [AdminImportController::class, 'store'])->name('import.store');

    // GL Integration
    Route::get('gl-import', [AdminGlImportController::class, 'index'])->name('gl-import.index');
    Route::post('gl-import', [AdminGlImportController::class, 'store'])->name('gl-import.store');
    Route::get('gl-import/{import}', [AdminGlImportController::class, 'show'])->name('gl-import.show');
    Route::post('gl-import/{import}/recompute', [AdminGlImportController::class, 'recompute'])->name('gl-import.recompute');
    Route::resource('gl-accounts', AdminGlAccountController::class)->only(['index', 'edit', 'update']);
    Route::post('gl-accounts/auto-map', [AdminGlAccountController::class, 'autoMap'])->name('gl-accounts.auto-map');
    Route::resource('area-aliases', AdminAreaAliasController::class)->except(['show']);

    // QBO Integration
    Route::get('qbo', [AdminQboController::class, 'index'])->name('qbo.index');
    Route::get('qbo/connect', [AdminQboController::class, 'connect'])->name('qbo.connect');
    Route::get('qbo/callback', [AdminQboController::class, 'callback'])->name('qbo.callback');
    Route::post('qbo/disconnect', [AdminQboController::class, 'disconnect'])->name('qbo.disconnect');
    Route::post('qbo/sync', [AdminQboController::class, 'sync'])->name('qbo.sync');

    // Budget Buckets
    Route::resource('budget-buckets', AdminBudgetBucketController::class);
    Route::get('bucket-amounts', [AdminBucketAmountController::class, 'index'])->name('bucket-amounts.index');
    Route::put('bucket-amounts', [AdminBucketAmountController::class, 'update'])->name('bucket-amounts.update');
    Route::resource('starting-cash', AdminStartingCashController::class)->only(['index', 'edit', 'update']);
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);

    // Design Panel
    Route::get('design', [AdminDesignController::class, 'index'])->name('design.index');
    Route::put('design', [AdminDesignController::class, 'update'])->name('design.update');
    Route::post('design/apply-preset', [AdminDesignController::class, 'applyPreset'])->name('design.apply-preset');
    Route::post('design/save-preset', [AdminDesignController::class, 'savePreset'])->name('design.save-preset');
    Route::delete('design/preset/{preset}', [AdminDesignController::class, 'deletePreset'])->name('design.delete-preset');
});

// GL Drill-down API
Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::get('gl/line-item-detail', [GlDrilldownController::class, 'lineItemDetail'])->name('api.gl.line-item-detail');
    Route::get('gl/account-transactions', [GlDrilldownController::class, 'accountTransactions'])->name('api.gl.account-transactions');
});

require __DIR__.'/auth.php';

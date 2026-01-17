<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev/migrate', function () {
    if (!App::environment('local')) {
        abort(403, 'This route is only available in local environment.');
    }
    try {
        Artisan::call('migrate', ['--force' => true]);
        return "Migration executed successfully.";
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});

Route::get('/dev/seed', function () {
    if (!App::environment('local')) {
        abort(403, 'This route is only available in local environment.');
    }
    try {
        Artisan::call('db:seed');
        return "Database seeded successfully!";
    } catch (\Exception $e) {
        return "Error seeding database: " . $e->getMessage();
    }
});

Route::get('/dev/seed-dummy', function () {
    if (!App::environment('local')) {
        abort(403, 'This route is only available in local environment.');
    }
    try {
        \Artisan::call('db:seed', ['--force' => true]);
        return "Database seeded successfully (Dummy Data)!";
    } catch (\Exception $e) {
        return "Error seeding database: " . $e->getMessage();
    }
});

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('profile', 'ProfileController@index');
    Route::get('profile/edit', 'ProfileController@edit');
    Route::patch('profile/{user}', 'ProfileController@update');

    Route::get('members/get-json', 'MemberController@jsonMembers');
    Route::resource('members', 'MemberController');

    Route::get('deposits/get-json', 'DepositController@jsonDeposits');
    Route::resource('deposits', 'DepositController')->only([
        'index', 'create', 'store', 'show', 'destroy'
    ]);

    Route::get('withdrawals/get-json', 'WithdrawalController@jsonWithdrawals');
    Route::resource('withdrawals', 'WithdrawalController')->only([
        'index', 'create', 'store', 'show', 'destroy'
    ]);

    Route::get('mutations', 'MutationController@index');
    Route::get('mutations/check-mutations', 'MutationController@check_mutations');

    Route::get('bankinterests', 'BankInterestController@index');
    Route::get('bankinterests/calculate/{member}', 'BankInterestController@calculate');
    Route::get('bankinterests/get-list', 'BankInterestController@jsonList');
    Route::get('bankinterests/get-history-interests/{member}', 'BankInterestController@jsonHistoryInterests');
    Route::get('bankinterests/check-interest', 'BankInterestController@check_interest');
    Route::post('bankinterests', 'BankInterestController@store');

    Route::get('nasabahs/get-json', 'NasabahController@jsonNasabah')->name('nasabahs.get-json');
    Route::resource('nasabahs', 'NasabahController');

    // Loans
    Route::post('loans/calculate', 'LoanController@calculate')->name('loans.calculate');
    Route::post('loans/{loan}/approve', 'LoanController@approve')->name('loans.approve');
    Route::post('loans/{loan}/reject', 'LoanController@reject')->name('loans.reject');
    Route::post('loans/{loan}/disburse', 'LoanController@disburse')->name('loans.disburse');
    Route::post('loans/{loan}/mark-bad-debt', 'LoanController@markBadDebt')->name('loans.markBadDebt');
    Route::post('loans/installments/{installment}/pay', 'LoanController@payInstallment')->name('loans.installments.pay');
    Route::get('loans/installments/{installment}/print', 'LoanController@printReceipt')->name('loans.installments.print');
    Route::post('loans/installments/{installment}/penalty', 'LoanController@addPenalty')->name('loans.installments.penalty');
    Route::post('loans/{loan}/collaterals/{collateral}/return', 'CollateralController@returnCollateral')->name('loans.collaterals.return');
    Route::resource('loans.collaterals', 'CollateralController');
    Route::resource('loans', 'LoanController');

    // Collection / Penagihan
    Route::get('collections', 'CollectionController@index')->name('collections.index');
    Route::get('collections/data', 'CollectionController@data')->name('collections.data');
    Route::post('collections/log', 'CollectionController@storeLog')->name('collections.log.store');
    Route::get('collections/queue', 'CollectionController@fieldQueue')->name('collections.queue');
    Route::post('collections/queue', 'CollectionController@addToFieldQueue')->name('collections.queue.store');
    Route::patch('collections/queue/{id}', 'CollectionController@updateFieldQueueStatus')->name('collections.queue.update');
    Route::post('collections/refresh', 'CollectionController@refreshCollectibility')->name('collections.refresh');

    // Accounting
    Route::get('accounting/coa', 'AccountingController@coa')->name('accounting.coa');
    Route::get('accounting/coa/data', 'AccountingController@coaData')->name('accounting.coa.data');
    Route::get('accounting/coa/create', 'AccountingController@coaCreate')->name('accounting.coa.create');
    Route::post('accounting/coa', 'AccountingController@coaStore')->name('accounting.coa.store');
    Route::get('accounting/coa/{id}/edit', 'AccountingController@coaEdit')->name('accounting.coa.edit');
    Route::put('accounting/coa/{id}', 'AccountingController@coaUpdate')->name('accounting.coa.update');

    Route::get('accounting/journals', 'AccountingController@journals')->name('accounting.journals');
    Route::get('accounting/journals/data', 'AccountingController@journalsData')->name('accounting.journals.data');
    Route::get('accounting/journals/create', 'AccountingController@createJournal')->name('accounting.journals.create');
    Route::post('accounting/journals', 'AccountingController@storeJournal')->name('accounting.journals.store');

    Route::get('accounting/cash-book', 'AccountingController@cashBook')->name('accounting.cash_book');
    Route::get('accounting/cash-book/data', 'AccountingController@cashBookData')->name('accounting.cash_book.data');

    Route::get('accounting/reports/neraca', 'AccountingController@neraca')->name('accounting.reports.neraca');
    Route::get('accounting/reports/laba-rugi', 'AccountingController@labaRugi')->name('accounting.reports.laba_rugi');
    Route::get('accounting/reports/arus-kas', 'AccountingController@arusKas')->name('accounting.reports.arus_kas');

    // Reports Module
    Route::prefix('reports')->group(function () {
        Route::get('/', 'ReportController@index')->name('reports.index');
        Route::get('/outstanding', 'ReportController@outstanding')->name('reports.outstanding');
        Route::get('/bad-debt', 'ReportController@badDebt')->name('reports.bad_debt');
        Route::get('/collateral', 'ReportController@collateral')->name('reports.collateral');
        Route::get('/cash-flow', 'ReportController@cashFlow')->name('reports.cash_flow');
        Route::get('/revenue', 'ReportController@revenue')->name('reports.revenue');
    });

    Route::get('notifications/mark-all-read', 'NotificationController@markAllAsRead')->name('notifications.readAll');

    // Settings
    Route::get('settings', 'SettingController@index')->name('settings.index');
    Route::post('settings', 'SettingController@update')->name('settings.update');
    Route::delete('settings/remove-logo', 'SettingController@removeLogo')->name('settings.remove_logo');
    Route::delete('settings/remove-background', 'SettingController@removeBackground')->name('settings.remove_background');
});

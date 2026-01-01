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

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/lang/{locale}', 'HomeController@lang')->name('lang');

Route::middleware(['auth'])->group(function () {
    Route::get('profile', 'ProfileController@index');
    Route::get('profile/edit', 'ProfileController@edit');
    Route::patch('profile/{user}', 'ProfileController@update');

    Route::get('members/get-json', 'MemberController@jsonMembers');
    Route::resource('members', 'MemberController');

    Route::get('deposits/get-json', 'DepositController@jsonDeposits');
    Route::resource('deposits', 'DepositController')->only([
        'index', 'create', 'store', 'show'
    ]);

    Route::get('withdrawals/get-json', 'WithdrawalController@jsonWithdrawals');
    Route::resource('withdrawals', 'WithdrawalController')->only([
        'index', 'create', 'store', 'show'
    ]);

    Route::get('mutations', 'MutationController@index');
    Route::get('mutations/check-mutations', 'MutationController@check_mutations');

    Route::get('bankinterests', 'BankInterestController@index');
    Route::get('bankinterests/calculate/{member}', 'BankInterestController@calculate');
    Route::get('bankinterests/get-members', 'BankInterestController@jsonMembers');
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
    Route::post('loans/installments/{installment}/penalty', 'LoanController@addPenalty')->name('loans.installments.penalty');
    Route::post('loans/{loan}/collaterals/{collateral}/return', 'CollateralController@returnCollateral')->name('loans.collaterals.return');
    Route::resource('loans.collaterals', 'CollateralController');
    Route::resource('loans', 'LoanController');
});

<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\ContestRegistrationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Payment\RegistrationPaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', [PageController::class, 'home'])->name('home');
//Route::get('/temp',[PageController::class, 'temp'] )->name('temp');
Route::get('/update-payment', [PageController::class, 'updatePayment'])->middleware(['auth', 'verified'])->name('update-payment');

Route::get('/php', function () {
    return phpinfo();
})->name('php');

require __DIR__ . '/auth.php';

//Route::get('/registration/create', [RegistrationController::class,'create'])->middleware(['auth', 'verified'])->name('registration.create');
//Route::get('/registration/my-registration', [RegistrationController::class,'myRegistration'])->middleware(['auth', 'verified'])->name('registration.my-registration');


//
//Route::get('/registration/{registration}/payment', [RegistrationPaymentController::class, 'payment'])->name('registration.payment.create');
//Route::get('/registration/{registration}/success', [RegistrationPaymentController::class, 'success'])->name('registration.payment.success');
//Route::get('/registration/{registration}/cancel', [RegistrationPaymentController::class, 'cancel'])->name('registration.payment.cancel');


Route::prefix('pages')->middleware([])->group(function () {
    Route::get('faq', [PageController::class, 'faq'])->name('pages.faq');
    Route::get('about', [PageController::class, 'about'])->name('pages.about');
    Route::get('contact', [PageController::class, 'contact'])->name('pages.contact');
    Route::get('privacy-policy', [PageController::class, 'privacyPolicy'])->name('pages.privacy-policy');
    Route::get('terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('pages.terms-and-conditions');
});
Route::get('incentive', [PageController::class, 'incentive'])->middleware(['auth'])->name('incentive');

Route::name('contests.')->group(callback: function () {
    Route::get('/all', [ContestController::class, 'index'])->name('index');
    Route::get('/{contest:slug}', [ContestController::class, 'show'])->middleware([])->name('show');
    Route::prefix('{contest:slug}/registrations')->name('registrations.')->group(callback: function () {
        Route::get('/', [ContestRegistrationController::class, 'index'])->middleware([])->name('index');
        Route::get('/section-wise', action: [ContestRegistrationController::class, 'section'])->middleware([])->name('section');
        Route::get('/{section}', [ContestRegistrationController::class, 'section'])->middleware([])->name('section.old');
    });
    Route::prefix('{contest:slug}/registration')->name('registration.')->middleware(['auth', 'verified'])->group(callback: function () {
        Route::get('/form', [RegistrationController::class, 'create'])->middleware([])->name('form');
        Route::get('/myRegistration', [RegistrationController::class, 'myRegistration'])->middleware([])->name('myRegistration');

    });
});



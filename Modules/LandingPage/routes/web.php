<?php

use Illuminate\Support\Facades\Route;
use Modules\LandingPage\Http\Controllers\LandingPageController;
use Modules\LandingPage\Http\Controllers\PaymentController;

Route::middleware(['web'])->group(function () {
    Route::resource('landingpages', 'Modules\LandingPage\Http\Controllers\LandingPageController')->names('landingpage');
    Route::post('contact', [LandingPageController::class, 'storeContact'])->name('contact.store');
    Route::get('contact', [LandingPageController::class, 'showContact'])->name('contact');
    Route::get('privacy', [LandingPageController::class, 'showPrivacy'])->name('privacy');
    
    // Routes d'inscription et de commande
    Route::get('register', [LandingPageController::class, 'showRegistration'])->name('register');
    Route::post('register', [LandingPageController::class, 'register'])->name('register.store');
    Route::get('order/payment/{order}', [LandingPageController::class, 'showPayment'])->name('order.payment');
    Route::post('order/success/{order}', [LandingPageController::class, 'orderSuccess'])->name('order.success');
    Route::get('order/success/{order}', [LandingPageController::class, 'showOrderSuccess'])->name('order.success.view'); 

    // Genius Pay Routes
    Route::get('order/pay/genius/{order}', 'Modules\LandingPage\Http\Controllers\PaymentController@initiate')->name('order.pay.genius');
    Route::get('order/payment/callback/{order}', 'Modules\LandingPage\Http\Controllers\PaymentController@callback')->name('order.payment.callback');
    Route::get('simulateur', [LandingPageController::class, 'simulateur'])->name('simulateur');
});
    

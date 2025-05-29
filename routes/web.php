<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\taxController;

use App\Http\Controllers\rentController;

use App\Http\Controllers\unitController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BranchController;

use App\Http\Controllers\tenantController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\paymentController;

use App\Http\Controllers\receiptController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\landlordController;

use App\Http\Controllers\propertyController;
use App\Http\Controllers\DashboardController;


use App\Http\Controllers\monitoringContoller;
use App\Http\Controllers\SelfPaymentController;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\changePasswordController;
use App\Http\Controllers\NotificationController; // Add this line at the top

Route::controller(AuthenticationController::class)->group(function () {
    Route::get('/', 'signin')->name('signin');
    Route::post('/', 'login')->name('signin.handler');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(changePasswordController::class)->group(function () {
    Route::post('/change-password', 'changePassword')->name('change.password');
});
Route::controller(PropertyController::class)->prefix('property')->middleware(['auth.admin'])->group(function () {
    Route::get('/index', 'index')->name('property.index');
    Route::get('/create/{id}', 'create')->name('property.create');
    Route::post('/store', 'store')->name('property.store');
    Route::get('/edit/{id}', 'edit')->name('property.edit');
    Route::get('/show/{id}', 'show')->name('property.show');
    Route::put('/update/{id}', 'update')->name('property.update');
    Route::get('/delete/{id}', 'destroy')->name('property.delete');
    Route::get('/report', 'report')->name('property.report');
    Route::get('/report-details', 'reportDetails')->name('property.report.fech');
    Route::get('/property/report/pdf', 'exportPdf')->name('property.report.print');
    Route::get('/property/{id}', 'show')->name('property.show');
    Route::get('/branches/{districtId}', 'getBranches')->name('property.branches');
    Route::get('/create/property', 'propertyCreate')->name('property.create.landlord');
    Route::post('/store/property', 'propertyStore')->name('property.store.landlord');
    //search property
    Route::get('/search', 'search')->name('property.lanlord.search');
});
Route::controller(rentController::class)->prefix('rent')->middleware(['auth.admin'])->group(function () {
    Route::get('/index', 'index')->name('rent.index');
    Route::get('/create', 'create')->name('rent.create');
    Route::post('/store', 'store')->name('rent.store');
    Route::get('/edit/{id}', 'edit')->name('rent.edit');
    Route::put('/update/{id}', 'update')->name('rent.update');
    Route::get('/delete/{id}', 'destroy')->name('rent.delete');
    Route::get('/report', 'report')->name('rent.report');
    Route::get('/report-details', 'reportDetails')->name('rent.report.fech');
    Route::get('/rent/report/pdf', 'exportPdf')->name('rent.report.print');
    Route::get('/rent/property/search', 'search')->name('rent.property.search');
});


// Authentication
Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword');
        Route::get('/signup', 'signup')->name('signup');
    });
});
Route::prefix('district')->middleware(['auth.admin'])->group(function () {
    Route::controller(DistrictController::class)->group(function () {
        Route::get('/index', 'index')->name('district.index');
        Route::get('/create', 'create')->name('district.create');
        Route::post('/store', 'store')->name('district.store');
        Route::get('/edit/{District}', 'edit')->name('district.edit');
        Route::put('/update/{District}', 'update')->name('district.update');
        Route::get('/delete/{District}', 'destroy')->name('district.delete');
    });
});

Route::prefix('branch')->middleware(['auth.admin'])->group(function () {
    Route::controller(BranchController::class)->group(function () {
        Route::get('/index', 'index')->name('branch.index');
        Route::get('/create', 'create')->name('branch.create');
        Route::post('/store', 'store')->name('branch.store');
        Route::get('/edit/{branch}', 'edit')->name('branch.edit');
        Route::put('/update/{branch}', 'update')->name('branch.update');
        Route::get('/delete/{branch}', 'destroy')->name('branch.delete');
    });
});


Route::prefix('tenant')->middleware(['auth.admin'])->group(function () {
    Route::controller(tenantController::class)->group(function () {
        Route::get('/index', 'index')->name('tenant.index');
        Route::get('/create', 'create')->name('tenant.create');
        Route::post('/store', 'store')->name('tenant.store');
        Route::get('/edit/{tenant}', 'edit')->name('tenant.edit');
        Route::put('/update/{tenant}', 'update')->name('tenant.update');
        Route::get('/delete/{tenant}', 'destroy')->name('tenant.delete');
        Route::get('/search', 'search')->name('tenant.search');
    });
});

Route::prefix('payment')->middleware(['auth.admin'])->group(function () {
    Route::controller(paymentController::class)->group(function () {
        Route::get('/index', 'index')->name('payment.index');
        Route::get('/index/tax', 'taxIndex')->name('payment.index.tax');
        Route::get('/create', 'create')->name('payment.create');
        Route::get('/create/tax', 'taxCreate')->name('payment.create.tax');
        Route::post('/store', 'store')->name('payment.store');
        Route::post('tax/store', 'taxStore')->name('tax.payment.store');
        Route::get('/edit/{payment}', 'edit')->name('payment.edit');
        Route::put('/update/{payment}', 'update')->name('payment.update');
        Route::get('/delete/{payment}', 'destroy')->name('payment.delete');
        Route::get('/search/tax-code', 'searchTax')->name('tax.payment.search');
        Route::get('/get-payment-amount/{tenantId}/{paymentType}', 'getPaymentAmount')->name('tenant.payment.getPaymentAmount');

        Route::get('/search', 'search')->name('tenant.payment.search');
    });
});


//self payment url
Route::prefix('self-payment')->group(function () {
    Route::controller(SelfPaymentController::class)->group(function () {
        Route::get('/{payment}', 'selfPayment')->name('self.payment');
        Route::get('/success/{payment}', 'success')->name('sucess.payment');
        Route::get('/fail/{payment}', 'fail')->name('self.payment');
        Route::get('/retry/{payment}', 'retry')->name('retry.payment');
    });
});
Route::prefix('lanlord')->middleware(['auth.admin'])->group(function () {
    Route::controller(landlordController::class)->group(function () {
        Route::get('/index', 'index')->name('lanlord.index');
        Route::get('/create', 'create')->name('lanlord.create');
        Route::post('/store', 'store')->name('lanlord.store');
        Route::get('/show/{lanlord}', 'show')->name('landlord.show');
        Route::get('/edit/{lanlord}', 'edit')->name('lanlord.edit');
        Route::put('/update/{lanlord}', 'update')->name('lanlord.update');
        Route::get('/delete/{lanlord}', 'destroy')->name('lanlord.delete');
    });
});
Route::prefix('monitor')->middleware(['auth.admin'])->group(function () {
    Route::controller(monitoringContoller::class)->group(function () {
        Route::get('/index', 'index')->name('monitor.index');
        Route::get('/show/{id}', 'show')->name('monitor.show');
        Route::get('/unit-rent/{id}', 'rentIndex')->name('monitor.rent.index');
        Route::post('/unit-rent', 'rentStore')->name('monitor.rent.store');
        Route::post('/approve', 'approve')->name('monitor.approve');
    });
});

Route::prefix('unit')->middleware(['auth.admin'])->group(function () {
    Route::controller(unitController::class)->group(function () {
        Route::get('/index', 'index')->name('unit.index');
        Route::get('/create/{id}', 'create')->name('unit.create');
        Route::post('/store', 'store')->name('unit.store');
        Route::get('/show/{unit}', 'show')->name('unit.show');
        Route::get('/edit/{unit}', 'edit')->name('unit.edit');
        Route::put('/update/{unit}', 'update')->name('unit.update');
        Route::get('/delete/{unit}', 'destroy')->name('unit.delete');
        Route::get('/search', 'search')->name('unit.property.search');
        Route::get('/unit-rent/details/{id}', 'viewRent')->name('monitor.rent.view');
    });
});





Route::prefix('tax')->middleware(['auth.admin'])->group(function () {
    Route::controller(taxController::class)->group(function () {
        Route::get('/index', 'index')->name('tax.index');
        Route::get('/create', 'create')->name('tax.create');
        Route::post('/store', 'store')->name('tax.store');
        Route::get('/edit/{tax}', 'edit')->name('tax.edit');
        Route::put('/update/{tax}', 'update')->name('tax.update');
        Route::get('/delete/{tax}', 'destroy')->name('tax.delete');
        Route::get('/property/search', 'search')->name('property.tax.search');
    });
});


// Dashboard
Route::prefix('dashboard')->middleware(['auth.admin'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
    });
});


Route::prefix('invoice')->middleware(['auth.admin'])->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
        Route::get('/pay/{id}', 'pay')->name('invoice.pay');
        Route::get('/search', 'search')->name('invoice.search');
        Route::post('/tax/generate', 'generateTaxInvoice')->name('generate.invoice.tax');
        Route::get('/tax/generate/{id}', 'invoice')->name('invoice.tax');
        Route::get('/store', 'store')->name('invoice.store');
        Route::get('/create', 'create')->name('invoice.create');
        Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
        Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
        Route::get('/paid-invoice-list', 'paidInvoiceList')->name('invoice.paid'); // New route for paid invoices
        Route::post('/generate', 'generateInvoice')->name('invoice.generate');
        Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
        Route::post('/q-1', 'quarter1')->name('invoice.quarter1');
        Route::post('/transaction', 'transaction')->name('invoice.transaction');
        Route::get('/property/{id}', 'propertyDetails')->name('invoice.property.details');
    });
});


// receipt
Route::prefix('receipt')->group(function () {
    Route::controller(receiptController::class)->group(function () {
        Route::get('tax/receipt/{id}', 'taxReceipt')->name('receipt.tax');
        Route::get('tax/rent/{id}', 'rentReceipt')->name('receipt.rent');
    });
});


// Users
Route::prefix('users')->middleware(['auth.admin'])->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/add-user', 'create')->name('user.create');
        Route::get('/users-grid', 'usersGrid')->name('usersGrid');
        Route::get('/users-list', 'index')->name('user.index');
        Route::get('/users-edit/{id}', 'edit')->name('user.edit');
        Route::get('/users-delete/{id}', 'delete')->name('user.delete');
        Route::put('/users-update/{id}', 'update')->name('user.update');

        Route::post('/users-store', 'store')->name('user.store');
        Route::get('/view-profile', 'viewProfile')->name('viewProfile');
    });
});


// Users
Route::prefix('otp')->group(function () {
    Route::controller(OtpController::class)->group(function () {
        Route::get('/show-otp', 'index')->name('otp.index');
        Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
    });
});

// Users
Route::middleware(['auth.admin'])->group(function () {
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/renotify/{propertyId}', 'reNotify')->name('renotify');
        Route::get('/history/{propertyId}', 'history')->name('history'); // Optional
    });
});

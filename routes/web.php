<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\taxController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\rentController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\tenantController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\taxRateController;
use App\Http\Controllers\businessController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\landlordController;
use App\Http\Controllers\propertyController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\monitoringContoller;
use App\Http\Controllers\AiapplicationController;
use App\Http\Controllers\ComponentpageController;
use App\Http\Controllers\RoleandaccessController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\changePasswordController;
use App\Http\Controllers\CryptocurrencyController;


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
    Route::get('/create', 'create')->name('property.create');
    Route::post('/store', 'store')->name('property.store');
    Route::get('/edit/{id}', 'edit')->name('property.edit');
    Route::put('/update/{id}', 'update')->name('property.update');
    Route::get('/delete/{id}', 'destroy')->name('property.delete');
    Route::get('/report', 'report')->name('property.report');
    Route::get('/report-details', 'reportDetails')->name('property.report.fech');
    Route::get('/property/report/pdf', 'exportPdf')->name('property.report.print');
    Route::get('/property/{id}', 'show')->name('property.show');

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

Route::controller(HomeController::class)->group(function () {
    Route::get('calendar', 'calendar')->name('calendar');
    Route::get('chatmessage', 'chatMessage')->name('chatMessage');
    Route::get('chatempty', 'chatempty')->name('chatempty');
    Route::get('email', 'email')->name('email');
    Route::get('error', 'error1')->name('error');
    Route::get('faq', 'faq')->name('faq');
    Route::get('gallery', 'gallery')->name('gallery');
    Route::get('kanban', 'kanban')->name('kanban');
    Route::get('pricing', 'pricing')->name('pricing');
    Route::get('termscondition', 'termsCondition')->name('termsCondition');
    Route::get('widgets', 'widgets')->name('widgets');
    Route::get('chatprofile', 'chatProfile')->name('chatProfile');
    Route::get('veiwdetails', 'veiwDetails')->name('veiwDetails');
    Route::get('blankPage', 'blankPage')->name('blankPage');
    Route::get('comingSoon', 'comingSoon')->name('comingSoon');
    Route::get('maintenance', 'maintenance')->name('maintenance');
    Route::get('starred', 'starred')->name('starred');
    Route::get('testimonials', 'testimonials')->name('testimonials');
});

// aiApplication
Route::prefix('aiapplication')->group(function () {
    Route::controller(AiapplicationController::class)->group(function () {
        Route::get('/codegenerator', 'codeGenerator')->name('codeGenerator');
        Route::get('/codegeneratornew', 'codeGeneratorNew')->name('codeGeneratorNew');
        Route::get('/imagegenerator', 'imageGenerator')->name('imageGenerator');
        Route::get('/textgeneratornew', 'textGeneratorNew')->name('textGeneratorNew');
        Route::get('/textgenerator', 'textGenerator')->name('textGenerator');
        Route::get('/videogenerator', 'videoGenerator')->name('videoGenerator');
        Route::get('/voicegenerator', 'voiceGenerator')->name('voiceGenerator');
    });
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
        //tenant search
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
        //tax search

        Route::get('/search/tax-code', 'searchTax')->name('tax.payment.search');
        Route::get('/get-payment-amount/{tenantId}/{paymentType}', 'getPaymentAmount')->name('tenant.payment.getPaymentAmount');

        Route::get('/search', 'search')->name('tenant.payment.search');
    });
});


Route::prefix('business')->middleware(['auth.admin'])->group(function () {
    Route::controller(businessController::class)->group(function () {
        Route::get('/index', 'index')->name('business.index');
        Route::get('/create', 'create')->name('business.create');
        Route::post('/store', 'store')->name('business.store');
        Route::get('/edit/{business}', 'edit')->name('business.edit');
        Route::put('/update/{business}', 'update')->name('business.update');
        Route::get('/delete/{business}', 'destroy')->name('business.delete');
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
        Route::post('/approve', 'approve')->name('monitor.approve');

    });
});

Route::prefix('invoice')->middleware(['auth.admin'])->group(function () {
    Route::controller(invoiceController::class)->group(function () {
        Route::get('/index', 'index')->name('invoice.index');
    });
});



Route::prefix('tax')->middleware(['auth.admin'])->group(function () {
    Route::prefix('rate')->group(function () {
        Route::controller(taxRateController::class)->group(function () {
            Route::get('/index', 'index')->name('tax.rate.index');
            Route::get('/create', 'create')->name('tax.rate.create');
            Route::post('/store', 'store')->name('tax.rate.store');
            Route::get('/edit/{taxRate}', 'edit')->name('tax.rate.edit');
            Route::put('/update/{taxRate}', 'update')->name('tax.rate.update');
            Route::get('/delete/{taxRate}', 'destroy')->name('tax.rate.delete');
        });

    });
    Route::controller(taxController::class)->group(function () {
        Route::get('/index', 'index')->name('tax.index');
        Route::get('/create', 'create')->name('tax.create');
        Route::post('/store', 'store')->name('tax.store');
        Route::get('/edit/{tax}', 'edit')->name('tax.edit');
        Route::put('/update/{tax}', 'update')->name('tax.update');
        //show


        Route::get('/delete/{tax}', 'destroy')->name('tax.delete');
        Route::get('/property/search', 'search')->name('property.tax.search');
    });
});
// chart
Route::prefix('chart')->group(function () {
    Route::controller(ChartController::class)->group(function () {
        Route::get('/columnchart', 'columnChart')->name('columnChart');
        Route::get('/linechart', 'lineChart')->name('lineChart');
        Route::get('/piechart', 'pieChart')->name('pieChart');
    });
});

// Componentpage
Route::prefix('componentspage')->group(function () {
    Route::controller(ComponentpageController::class)->group(function () {
        Route::get('/alert', 'alert')->name('alert');
        Route::get('/avatar', 'avatar')->name('avatar');
        Route::get('/badges', 'badges')->name('badges');
        Route::get('/button', 'button')->name('button');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/card', 'card')->name('card');
        Route::get('/carousel', 'carousel')->name('carousel');
        Route::get('/colors', 'colors')->name('colors');
        Route::get('/dropdown', 'dropdown')->name('dropdown');
        Route::get('/imageupload', 'imageUpload')->name('imageUpload');
        Route::get('/list', 'list')->name('list');
        Route::get('/pagination', 'pagination')->name('pagination');
        Route::get('/progress', 'progress')->name('progress');
        Route::get('/radio', 'radio')->name('radio');
        Route::get('/starrating', 'starRating')->name('starRating');
        Route::get('/switch', 'switch')->name('switch');
        Route::get('/tabs', 'tabs')->name('tabs');
        Route::get('/tags', 'tags')->name('tags');
        Route::get('/tooltip', 'tooltip')->name('tooltip');
        Route::get('/typography', 'typography')->name('typography');
        Route::get('/videos', 'videos')->name('videos');
    });
});

// Dashboard
Route::prefix('dashboard')->middleware(['auth.admin'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/index2', 'index2')->name('index2');
        Route::get('/index3', 'index3')->name('index3');
        Route::get('/index4', 'index4')->name('index4');
        Route::get('/index5', 'index5')->name('index5');
        Route::get('/index6', 'index6')->name('index6');
        Route::get('/index7', 'index7')->name('index7');
        Route::get('/index8', 'index8')->name('index8');
        Route::get('/index9', 'index9')->name('index9');
        Route::get('/index10', 'index10')->name('index10');
        Route::get('/wallet', 'wallet')->name('wallet');
    });
});




// Forms
Route::prefix('forms')->group(function () {
    Route::controller(FormsController::class)->group(function () {
        Route::get('/form-layout', 'formLayout')->name('formLayout');
        Route::get('/form-validation', 'formValidation')->name('formValidation');
        Route::get('/form', 'form')->name('form');
        Route::get('/wizard', 'wizard')->name('wizard');
    });
});

// invoice/invoiceList
Route::prefix('invoice')->group(function () {
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
        Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
        Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
        Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
    });
});

// Settings
Route::prefix('settings')->group(function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/company', 'company')->name('company');
        Route::get('/currencies', 'currencies')->name('currencies');
        Route::get('/language', 'language')->name('language');
        Route::get('/notification', 'notification')->name('notification');
        Route::get('/notification-alert', 'notificationAlert')->name('notificationAlert');
        Route::get('/payment-gateway', 'paymentGateway')->name('paymentGateway');
        Route::get('/theme', 'theme')->name('theme');
    });
});

// Table
Route::prefix('table')->group(function () {
    Route::controller(TableController::class)->group(function () {
        Route::get('/tablebasic', 'tableBasic')->name('tableBasic');
        Route::get('/tabledata', 'tableData')->name('tableData');
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
Route::prefix('otp')->middleware(['auth.admin'])->group(function () {
    Route::controller(OtpController::class)->group(function () {
        Route::get('/show-otp', 'index')->name('otp.index');
        Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
    });
});

// Users
Route::prefix('blog')->group(function () {
    Route::controller(BlogController::class)->group(function () {
        Route::get('/addBlog', 'addBlog')->name('addBlog');
        Route::get('/blog', 'blog')->name('blog');
        Route::get('/blogDetails', 'blogDetails')->name('blogDetails');
    });
});

// Users
Route::prefix('roleandaccess')->group(function () {
    Route::controller(RoleandaccessController::class)->group(function () {
        Route::get('/assignRole', 'assignRole')->name('assignRole');
        Route::get('/roleAaccess', 'roleAaccess')->name('roleAaccess');
    });
});

// Users
Route::prefix('cryptocurrency')->group(function () {
    Route::controller(CryptocurrencyController::class)->group(function () {
        Route::get('/marketplace', 'marketplace')->name('marketplace');
        Route::get('/marketplacedetails', 'marketplaceDetails')->name('marketplaceDetails');
        Route::get('/portfolio', 'portfolio')->name('portfolio');
        Route::get('/wallet', 'wallet')->name('wallet');
    });
});

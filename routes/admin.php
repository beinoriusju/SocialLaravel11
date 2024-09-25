<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\TranslationsController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\EventCategoryController;
use App\Http\Controllers\Backend\EventSubCategoryController;
use App\Http\Controllers\Backend\CalendarCategoryController;
use App\Http\Controllers\Backend\CalendarSubCategoryController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogSubCategoryController;
use App\Http\Controllers\Backend\UserDataTableController;

/** Admin Route Group */
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Simple role check without additional middleware
    Route::group(['middleware' => function ($request, $next) {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }], function () {

        /** Dashboard Route */
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        /** Profile Routes */
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/update/password', [ProfileController::class, 'updatePassword'])->name('password.update');

        /** Slider Routes */
        Route::resource('slider', SliderController::class);

        /** Translations Routes */
        Route::resource('translations', TranslationsController::class);

        /** Category Routes */
        Route::put('category/change-status', [CategoryController::class, 'changeStatus'])->name('category.change-status');
        Route::resource('category', CategoryController::class);

        /** Sub Category Routes */
        Route::put('subcategory/change-status', [SubCategoryController::class, 'changeStatus'])->name('sub-category.change-status');
        Route::resource('sub-category', SubCategoryController::class);

        /** Event Category Routes */
        Route::put('eventcategory/change-status', [EventCategoryController::class, 'changeStatus'])->name('eventcategory.change-status');
        Route::put('eventcategory/change-admin', [EventCategoryController::class, 'changeAdmin'])->name('eventcategory.change-admin');
        Route::resource('eventcategory', EventCategoryController::class);

        /** Event Sub Category Routes */
        Route::put('eventsubcategory/change-status', [EventSubCategoryController::class, 'changeStatus'])->name('eventsub-category.change-status');
        Route::resource('eventsub-category', EventSubCategoryController::class);

        /** Calendar Category Routes */
        Route::put('calendarcategory/change-status', [CalendarCategoryController::class, 'changeStatus'])->name('calendarcategory.change-status');
        Route::resource('calendarcategory', CalendarCategoryController::class);

        /** Calendar Sub Category Routes */
        Route::put('calendarsubcategory/change-status', [CalendarSubCategoryController::class, 'changeStatus'])->name('calendarsub-category.change-status');
        Route::resource('calendarsub-category', CalendarSubCategoryController::class);

        /** Blog Category Routes */
        Route::put('blogcategory/change-status', [BlogCategoryController::class, 'changeStatus'])->name('blogcategory.change-status');
        Route::resource('blogcategory', BlogCategoryController::class);

        /** Blog Sub Category Routes */
        Route::put('blogsubcategory/change-status', [BlogSubCategoryController::class, 'changeStatus'])->name('blogsub-category.change-status');
        Route::resource('blogsub-category', BlogSubCategoryController::class);

        Route::get('users', [UserDataTableController::class, 'index'])->name('users');
      

    });
});

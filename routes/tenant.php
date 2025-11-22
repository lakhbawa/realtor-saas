<?php

use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant (Subdomain) Routes
|--------------------------------------------------------------------------
|
| These routes are for the public-facing tenant websites accessed via
| subdomains (e.g., john.myrealtorsites.com).
|
*/

Route::middleware(['tenant'])->group(function () {
    Route::get('/', [PublicSiteController::class, 'home'])->name('tenant.home');
    Route::get('/properties', [PublicSiteController::class, 'properties'])->name('tenant.properties');
    Route::get('/properties/{slug}', [PublicSiteController::class, 'property'])->name('tenant.property');
    Route::get('/about', [PublicSiteController::class, 'about'])->name('tenant.about');
    Route::get('/contact', [PublicSiteController::class, 'contact'])->name('tenant.contact');
    Route::post('/contact', [PublicSiteController::class, 'submitContact'])->name('tenant.contact.submit');

    // Blog routes
    Route::get('/blog', [PublicSiteController::class, 'blog'])->name('tenant.blog');
    Route::get('/blog/{slug}', [PublicSiteController::class, 'blogPost'])->name('tenant.blog.post');

    // Custom pages (must be last as it catches slugs)
    Route::get('/page/{slug}', [PublicSiteController::class, 'page'])->name('tenant.page');
});

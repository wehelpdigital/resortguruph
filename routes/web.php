<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\GoldPointsController;
use App\Http\Controllers\Dashboard\HistoryController;
use App\Http\Controllers\Dashboard\ListingsController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\ResortsController;
use App\Http\Controllers\Dashboard\StubController;
use App\Http\Controllers\DestinationsController;
use App\Http\Controllers\FoodTripController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KeywordPageController;
use App\Http\Controllers\ResortPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinations', [DestinationsController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{cluster}', [DestinationsController::class, 'cluster'])->name('destinations.cluster')->where('cluster', '[a-z0-9-]+');
Route::get('/food-trip', [FoodTripController::class, 'index'])->name('food-trip.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{post:slug}/comment', [BlogController::class, 'storeComment'])
    ->middleware('auth:owner')
    ->name('blog.comments.store');
Route::get('/listing/{resort:slug}', [ResortPageController::class, 'show'])->name('resort.show');

// Contact form
Route::get('/contact', [StaticPageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Static pages (about/terms/privacy) — handled via single route
Route::get('/about', fn() => app(StaticPageController::class)->show('about'))->name('about');
Route::get('/terms', fn() => app(StaticPageController::class)->show('terms'))->name('terms');
Route::get('/privacy', fn() => app(StaticPageController::class)->show('privacy'))->name('privacy');

// Sitemap / robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// ============ AUTH (owner guard) ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============ DASHBOARD (auth) ============
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Resorts CRUD
    Route::get('/resorts', [ResortsController::class, 'index'])->name('resorts.index');
    Route::get('/resorts/create', [ResortsController::class, 'create'])->name('resorts.create');
    Route::post('/resorts', [ResortsController::class, 'store'])->name('resorts.store');
    Route::get('/resorts/{resort}/edit', [ResortsController::class, 'edit'])->name('resorts.edit');
    Route::put('/resorts/{resort}', [ResortsController::class, 'update'])->name('resorts.update');
    Route::post('/resorts/{resort}/submit', [ResortsController::class, 'submitForReview'])->name('resorts.submit');
    Route::delete('/resorts/{resort}', [ResortsController::class, 'destroy'])->name('resorts.destroy');
    Route::post('/resorts/{resort}/media', [ResortsController::class, 'uploadMedia'])->name('resorts.media.upload');
    Route::delete('/resorts/{resort}/media/{media}', [ResortsController::class, 'deleteMedia'])->name('resorts.media.delete');

    // Gold Points + topup
    Route::get('/gold-points', [GoldPointsController::class, 'index'])->name('gp.index');
    Route::get('/gold-points/topup', [GoldPointsController::class, 'topupForm'])->name('gp.topup');
    Route::post('/gold-points/topup', [GoldPointsController::class, 'topupSubmit'])->name('gp.topup.submit');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Listings + bidding (Phase 2a)
    Route::get('/listings', [ListingsController::class, 'index'])->name('listings');
    Route::get('/listings/browse', [ListingsController::class, 'browse'])->name('listings.browse');
    Route::get('/listings/claim/{keyword:slug}', [ListingsController::class, 'claimForm'])->name('listings.claim.form');
    Route::post('/listings/claim/{keyword:slug}', [ListingsController::class, 'claim'])->name('listings.claim');
    Route::post('/listings/{listing}/bid', [ListingsController::class, 'bid'])->name('listings.bid');

    // Stubs for deferred features
    Route::get('/restaurants', [StubController::class, 'restaurants'])->name('restaurants');
    Route::get('/adventures', [StubController::class, 'adventures'])->name('adventures');
    Route::get('/ai', [StubController::class, 'ai'])->name('ai');
    Route::get('/notifications', [StubController::class, 'notifications'])->name('notifications');
    Route::get('/tutorials', [StubController::class, 'tutorials'])->name('tutorials');
});

// Member-only review submission for destination/keyword pages
Route::post('/destination-review', [KeywordPageController::class, 'storeReview'])
    ->middleware('auth:owner')
    ->name('keyword.review.store');

// Builder preview — renders a single rg_content_blocks row in a standalone
// page with Tailwind + Splide so the mother admin can iframe it as a true
// miniature. Must sit BEFORE the keyword-page catch-all so the slug
// matcher doesn't claim "_preview" as a keyword.
Route::get('/_preview/block/{block}', [\App\Http\Controllers\BuilderPreviewController::class, 'show'])
    ->where('block', '[0-9]+')
    ->name('builder.preview.block');

// ============ ACTIVITIES HUB ============
// Top-level hub linking out to fiestas + every other tourist-activity
// category. Slug is intentionally long-tail / keyword-rich for SEO.
// Must sit BEFORE the keyword-page catch-all.
Route::get('/philippine-tourist-activities-adventures-what-to-do',
    [\App\Http\Controllers\ActivitiesController::class, 'index'])
    ->name('activities.index');

// ============ FOODS HUB ============
// Companion to the activities hub — lists every Filipino dish by
// category (popular staples, street food, exotics, regional
// specialties, sweets). The /food-trip route is the restaurant
// directory; this one is the dish directory.
Route::get('/filipino-food-dishes-what-to-eat',
    [\App\Http\Controllers\FoodsController::class, 'index'])
    ->name('foods.index');

// ============ BUYS / PASALUBONG HUB ============
// Companion to foods + activities — the unique-finds-to-bring-home
// guide. Heritage salts, regional textiles, artisanal crafts, and
// packaged sweets sourced to the province of origin.
Route::get('/philippine-souvenirs-pasalubong-what-to-buy',
    [\App\Http\Controllers\BuysController::class, 'index'])
    ->name('buys.index');

// ============ FIESTAS (activities vertical) ============
// List slug renamed from /fiestas to a keyword-rich slug for SEO.
// Detail pages keep the short /fiestas/{slug} form so individual
// festival URLs stay clean and shareable. Old /fiestas list URL is
// 301-redirected so any inbound links keep working.
Route::get('/philippine-fiestas-festivals-guide',
    [\App\Http\Controllers\FiestaController::class, 'index'])
    ->name('fiestas.index');
Route::get('/fiestas', fn() => redirect()->route('fiestas.index', [], 301));
Route::get('/fiestas/{fiesta:slug}',
    [\App\Http\Controllers\FiestaController::class, 'show'])
    ->name('fiestas.show');

// ============ KEYWORD PAGE CATCH-ALL (must be LAST) ============
Route::get('/{page:slug}', [KeywordPageController::class, 'show'])->name('keyword.show')
    ->where('keyword', '[a-z0-9-]+');

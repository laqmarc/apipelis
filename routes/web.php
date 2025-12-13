<?php

use App\Http\Controllers\TmdbController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TmdbController::class, 'trending'])->name('home');
Route::get('/movies/{id}', [TmdbController::class, 'show'])->name('movie.show');
Route::get('/people/popular', [TmdbController::class, 'popularPeople'])->name('people.popular');
Route::get('/people/{id}', [TmdbController::class, 'person'])->whereNumber('id')->name('person.show');
Route::get('/collections/{id}', [TmdbController::class, 'collection'])->name('collection.show');
Route::get('/companies/{id}', [TmdbController::class, 'company'])->name('company.show');
Route::get('/keywords/{id}', [TmdbController::class, 'keyword'])->name('keyword.show');
Route::get('/keywords', [TmdbController::class, 'keywords'])->name('keywords.index');
Route::get('/networks/{id}', [TmdbController::class, 'network'])->name('network.show');
Route::get('/search', [TmdbController::class, 'search'])->name('search');
Route::get('/search/suggest', [TmdbController::class, 'searchSuggest'])->name('search.suggest');


<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::get('/', [UiController::class, 'home'])->name('home');
Route::resource('products', ProductController::class)->except(['show']);
Route::resource('orders', OrderController::class)->except(['show']);

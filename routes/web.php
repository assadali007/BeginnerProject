<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\PostController as AdminPostController;

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
    $posts = Post::with('category', 'tags')->take(5)->latest()->get();

    return view('pages.home', compact('posts'));

    return view('pages.home');
})->name('home');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function(){
    Route::prefix('admin')->group(function(){
        Route::name('admin.')->group(function(){
     Route::resource('posts', AdminPostController::class);
    
        });
    
    
    });

});

Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('post/{id}', [PostController::class, 'show'])->name('posts.show');
Route::view('about', 'pages.about')->name('about');


require __DIR__.'/auth.php';


/*
Route::group(['middleware' => 'auth', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('tags', AdminTagController::class);
    Route::resource('posts', AdminPostController::class);
});
*/
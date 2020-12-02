<?php

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

use App\Enums\Routes;
Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web','ArticleMiddleware']],function (){
    Route::post(Routes::ARTICLE_POST,'ArticleController@articlePost');
    Route::post(Routes::ARTICLE_LIKE,'ArticleController@likeArticle');
    Route::post(Routes::ARTICLE_UNLIKE,'ArticleController@unlikeArticle');
    Route::any(Routes::ARTICLE_LIST,'ArticleController@showList');
    Route::post(Routes::ARTICLE_CENSOR,'ArticleController@actionCensor');
});

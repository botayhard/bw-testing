<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth'], function(){
    Route::get('/proposals', 'ProposalController@all');
    Route::get('/proposals/{proposal}', 'ProposalController@get');
    Route::post('/proposals/{proposal}/delete', 'ProposalController@delete');
    Route::post('/send_feedback','AjaxApi\MailController@testMail');
    Route::get('/proposals/all/count', 'ProposalController@count');
    Route::post('/proposals/history/createComment', 'HistoryController@createComment');
    Route::get('/proposals/history/getFromProposal/{proposal}', 'HistoryController@getFromProposal');

    /* Image upload */
    Route::post('/image', 'ImageController@store');

    /* Articles */
    Route::post('/article', 'ArticleController@store');
    Route::post('/article/{article}/update', 'ArticleController@update');
    Route::post('/article/{article}/delete', 'ArticleController@destroy');

    /* Comments */
    Route::get('/comment', 'CommentController@list');
    Route::post('/comment/{comment}/update', 'CommentController@update');
    Route::post('/comment/{comment}/delete', 'CommentController@destroy');

    router()->post('/metatag/create', 'MetatagController@create');
    router()->post('/tag/store', 'TagController@store');
    router()->post('/tag/update/{tag}', 'TagController@update');
    router()->post('/tag/delete/{tag}', 'TagController@delete');
    router()->post('/article/add/tag/{article}', 'ArticleController@addTag');
    router()->post('/article/tag/delete/{article}', 'ArticleController@deleteAllTags');

    router()->get('/users/all','UserController@all');
});

/* Articles */
Route::get('/article', 'ArticleController@index');
Route::get('/article/{slug}', 'ArticleController@show');
Route::get('/article/search/get', 'ArticleController@search');
router()->get('/article/tag/{article}', 'ArticleController@getTags');
router()->get('/article/main/get', 'ArticleController@getMainArticle');

/* Comments */
Route::get('/article/{article}/comment', 'CommentController@index');
Route::post('/article/{article}/comment', 'CommentController@store');

router()->post('/proposals/create', 'ProposalController@create');

router()->get('/user/authorize', 'UserController@isAuthorized');
router()->get('/login', 'Auth\LoginController@showLoginForm')->name('login');
router()->post('/login', 'Auth\LoginController@login');
router()->post('/logout', 'Auth\LoginController@logout')->name('logout');
router()->post('/register', 'Auth\RegisterController@register');

router()->get('/metatag/get/{metatag}', 'MetatagController@get');

router()->get('/tag/all', 'TagController@all');
router()->get('/tag/search/{tag}', 'TagController@search');


Route::get('/mic-check', function () {
    return 'sounds good';
});


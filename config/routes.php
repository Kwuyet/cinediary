<?php

$router->get('/', 'HomeController@index');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/auth/google', 'AuthController@googleRedirect');
$router->get('/auth/google/callback', 'AuthController@googleCallback');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/register/verify', 'AuthController@showRegisterVerify');
$router->post('/register/verify', 'AuthController@verifyRegisterOtp');
$router->post('/register/resend', 'AuthController@resendRegisterOtp');
// Forgot password (OTP reset)
$router->get('/forgot', 'AuthController@showForgot');
$router->post('/forgot', 'AuthController@sendForgotOtp');
$router->get('/forgot/verify', 'AuthController@showForgotVerify');
$router->post('/forgot/verify', 'AuthController@verifyForgotOtp');
$router->post('/forgot/resend', 'AuthController@resendForgotOtp');
$router->post('/logout', 'AuthController@logout');

$router->get('/movies', 'MovieController@index');
$router->get('/movies/{id}', 'MovieController@show');
$router->get('/discover', 'DiscoverController@index');
$router->get('/reviews', 'ReviewController@index');
$router->get('/lists', 'LibraryController@lists');
$router->post('/lists/{id}/items', 'LibraryController@storeListItem');
$router->post('/lists/{id}/items/{itemId}/update', 'LibraryController@updateListItem');
$router->post('/lists/{id}/items/{itemId}/delete', 'LibraryController@deleteListItem');
$router->get('/lists/{id}', 'LibraryController@listDetail');
$router->post('/lists', 'LibraryController@storeList');

$router->get('/movies/{id}/reviews/create', 'ReviewController@create');
$router->post('/movies/{id}/reviews', 'ReviewController@store');
$router->get('/reviews/{id}', 'ReviewController@show');
$router->get('/reviews/{id}/edit', 'ReviewController@edit');
$router->post('/reviews/{id}/update', 'ReviewController@update');
$router->post('/reviews/{id}/delete', 'ReviewController@destroy');

$router->post('/reviews/{id}/comments', 'CommentController@store');
$router->post('/comments/{id}/reply', 'CommentController@reply');
$router->post('/comments/{id}/update', 'CommentController@update');
$router->post('/comments/{id}/delete', 'CommentController@destroy');
$router->post('/comments/{id}/like', 'CommentController@like');

$router->get('/profile', 'ProfileController@show');
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile/update', 'ProfileController@update');
$router->get('/profile/password', 'ProfileController@passwordForm');
$router->post('/profile/password', 'ProfileController@updatePassword');
$router->get('/profile/{id}', 'ProfileController@show');

$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/reviews', 'AdminController@reviews');
// Admin review & comment management
$router->get('/admin/moderation', 'AdminController@moderationPanel');
$router->post('/admin/moderation/action', 'AdminController@moderationAction');
$router->get('/admin/moderation/detail', 'AdminController@moderationDetail');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/users/suggest', 'AdminController@usersSuggest');
$router->post('/admin/users/{id}/lock', 'AdminController@toggleUserLock');
$router->post('/admin/users/{id}/role', 'AdminController@changeUserRole');
$router->get('/admin/movies', 'AdminController@movies');
$router->get('/admin/movies/import', 'AdminController@moviesImportForm');
$router->post('/admin/movies/import', 'AdminController@moviesImportProcess');
$router->get('/admin/settings', 'AdminController@settings');
$router->get('/admin/movies/{id}/edit', 'AdminController@editMovie');
$router->post('/admin/movies/{id}/update', 'AdminController@updateMovie');
$router->post('/admin/movies/{id}/delete', 'AdminController@deleteMovie');

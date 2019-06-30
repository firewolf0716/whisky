<?php
ini_set('max_execution_time', 0);
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

use Symfony\Component\Console\Output\StreamOutput;

Route::get('/', function () {
	// $stream = fopen('php://output', 'w');
	// Artisan::call('scrape:auctions', array(), new StreamOutput($stream));
	// print '<p>DONE</p>';
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


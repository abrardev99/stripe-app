<?php

use App\Models\Purcahse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/home', function (Request $request) {
    $request->session()->flash('flash.banner', 'Yay it works!');
    $request->session()->flash('flash.bannerStyle', 'success');
    return redirect()->route('purchase.index');
})->name('home');

// webhook for successfully url.s
Route::post('/ordered', function (Request $request) {
    // process webhook
    if (($request->type == 'charge.succeeded') && $user = User::where('stripe_id', $request->data['object']['customer'])->first() ) {
        Purcahse::create([
            'user_id' => $user->id,
            'amount' => $request->data['object']['amount'],
            'created_at' =>  $request->created,
        ]);
    }
})->name('ordered');

Route::get('/dev-login', function () {
    auth()->login(User::first());
    return redirect()->route('dashboard');
})->name('dev.login');

Route::get('/charge-checkout', function (Request $request) {
    return $request->user()->checkoutCharge(1200, 'T-Shirt', 1);
})->name('charge.checkout');

Route::get('/purchase/index', function (){
    return view('purchase/index', ['purchases' => auth()->user()->purchases ]);
})->name('purchase.index');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

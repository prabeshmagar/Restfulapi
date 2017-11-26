<?php

use Illuminate\Http\Request;

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


/** Buyers */

Route::resource('buyers','Buyer\BuyerController',['only'=> ['index','show']]);

/**buyer transaction */
Route::resource('buyers.transactions','Buyer\BuyerTransactionController',['only'=> ['index']]);

/**buyer product */
Route::resource('buyers.products','Buyer\BuyerProductController',['only'=> ['index']]);

/**buyer seller */
Route::resource('buyers.sellers','Buyer\BuyerSellerController',['only'=> ['index']]);

/**buyer category */
Route::resource('buyers.categories','Buyer\BuyerCategoryController',['only'=> ['index']]);


/** Categories*/
Route::resource('categories','Category\CategoryController',['except'=> ['create','edit']]);

/**category product relation */
Route::resource('categories.products','Category\CategoryProductController',['only'=> ['index']]);

/**category sellers relation */
Route::resource('categories.sellers','Category\CategorySellerController',['only'=> ['index']]);

/**category transaction relation */
Route::resource('categories.transactions','Category\CategoryTransactionController',['only'=> ['index']]);

/**category Buyer relation */
Route::resource('categories.buyers','Category\CategoryBuyerController',['only'=> ['index']]);


/** Product*/
Route::resource('products','Product\ProductController',['only'=> ['index','show']]);

/** Seller*/
Route::resource('sellers','Seller\SellerController',['only'=> ['index','show']]);

/** Transactions*/
Route::resource('transactions','Transaction\TransactionController',['only'=> ['index','show']]);

/** Transactions Category*/
Route::resource('transactions.categories','Transaction\TransactionCategoryController',['only'=> ['index']]);

/** Transactions Sellers */
Route::resource('transactions.sellers','Transaction\TransactionSellerController',['only'=> ['index']]);

/** User*/
Route::resource('users','User\UserController',['except'=> ['create','edit']]);
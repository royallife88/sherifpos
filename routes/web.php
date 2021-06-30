<?php

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
    if (Auth::user()) {
        return redirect('/home');
    } else {
        return redirect('/login');
    }
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth', 'language', 'SetSessionData']], function () {

    Route::get('product/get-variation-row', 'ProductController@getVariationRow');
    Route::get('product/get-products', 'ProductController@getProducts');
    Route::resource('product', ProductController::class);
    Route::get('product-class/get-dropdown', 'ProductClassController@getDropdown');
    Route::resource('product-class', ProductClassController::class);
    Route::get('category/get-sub-category-dropdown', 'CategoryController@getSubCategoryDropdown');
    Route::get('category/get-dropdown', 'CategoryController@getDropdown');
    Route::resource('category', CategoryController::class);
    Route::get('brand/get-dropdown', 'BrandController@getDropdown');
    Route::resource('brand', BrandController::class);
    Route::get('unit/get-dropdown', 'UnitController@getDropdown');
    Route::resource('unit', UnitController::class);
    Route::get('color/get-dropdown', 'ColorController@getDropdown');
    Route::resource('color', ColorController::class);
    Route::get('size/get-dropdown', 'SizeController@getDropdown');
    Route::resource('size', SizeController::class);
    Route::get('grade/get-dropdown', 'GradeController@getDropdown');
    Route::resource('grade', GradeController::class);
    Route::get('tax/get-dropdown', 'TaxController@getDropdown');
    Route::resource('tax', TaxController::class);
    Route::get('barcode/add-product-row', 'BarcodeController@addProductRow');
    Route::get('barcode/print-barcode', 'BarcodeController@printBarcode');
    Route::resource('barcode', BarcodeController::class);

    Route::get('customer/get-dropdown', 'CustomerController@getDropdown');
    Route::resource('customer', CustomerController::class);
    Route::get('customer-type/get-dropdown', 'CustomerTypeController@getDropdown');
    Route::get('customer-type/get-product-discount-row', 'CustomerTypeController@getProductDiscountRow');
    Route::get('customer-type/get-product-point-row', 'CustomerTypeController@getProductPointRow');
    Route::resource('customer-type', CustomerTypeController::class);

    Route::resource('supplier', SupplierController::class);
    Route::resource('product-classification-tree', ProductClassificationTreeController::class);

    Route::get('store/get-dropdown', 'StoreController@getDropdown');
    Route::resource('store', StoreController::class);
    Route::post('user/check-password/{id}', 'UserController@checkPassword');
    Route::get('user/get-dropdown', 'UserController@getDropdown');
    Route::resource('user', UserController::class);

    Route::get('purchase-order/get-products', 'PurchaseOrderController@getProducts');
    Route::get('purchase-order/add-product-row', 'PurchaseOrderController@addProductRow');
    Route::get('purchase-order/get-po-number', 'PurchaseOrderController@getPoNumber');
    Route::get('purchase-order/draft-purchase-order', 'PurchaseOrderController@getDraftPurchaseOrder');
    Route::get('purchase-order/quick-add-draft', 'PurchaseOrderController@quickAddDraft');
    Route::resource('purchase-order', PurchaseOrderController::class);

    Route::get('add-stock/add-product-row', 'AddStockController@addProductRow');
    Route::get('add-stock/get-purchase-order-details/{id}', 'AddStockController@getPurchaseOrderDetails');
    Route::resource('add-stock', AddStockController::class);

    Route::get('transaction-payment/add-payment/{id}', 'TransactionPaymentController@addPayment');
    Route::resource('transaction-payment', TransactionPaymentController::class);

    Route::resource('store-pos', StorePosController::class);

    Route::get('pos/get-products', 'SellPosController@getProducts');
    Route::get('pos/add-product-row', 'SellPosController@addProductRow');
    Route::get('pos/get-product-items-by-filter/{id}/{type}', 'SellPosController@getProductItemsByFilter');
    Route::get('pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::resource('pos', SellPosController::class);
    Route::resource('cash-rgister', CashRegisterController::class);

    Route::get('coupon/get-details/{coupon_code}', 'CouponController@getDetails');
    Route::get('coupon/toggle-active/{id}', 'CouponController@toggleActive');
    Route::get('coupon/generate-code', 'CouponController@generateCode');
    Route::resource('coupon', CouponController::class);

    Route::get('gift-card/toggle-active/{id}', 'GiftCardController@toggleActive');
    Route::get('gift-card/generate-code', 'GiftCardController@generateCode');
    Route::get('gift-card/get-details/{gift_card_number}', 'GiftCardController@getDetails');
    Route::resource('gift-card', GiftCardController::class);





























Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:cache');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
});


});

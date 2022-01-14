<?php

use Illuminate\Support\Facades\Cache;
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
Route::group(['middleware' => ['language']], function () {
    Auth::routes();
});

Route::get('general/switch-language/{lang}', 'GeneralController@switchLanguage');
Route::group(['middleware' => ['auth', 'SetSessionData', 'language', 'timezone']], function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('get-dashboard-data/{start_date}/{end_date}', 'HomeController@getDashboardData');
    Route::get('get-help', 'HomeController@getHelp');
    Route::get('get-chart-and-table-section', 'HomeController@getChartAndTableSection');
    Route::get('my-transactions/{year}/{month}', 'HomeController@myTransaction');
    Route::get('my-holidays/{year}/{month}', 'HomeController@myHoliday');
    Route::get('general/view-uploaded-files/{model_name}/{model_id}', 'GeneralController@viewUploadedFiles');
    Route::get('product/get-variation-row', 'ProductController@getVariationRow');
    Route::get('product/get-products', 'ProductController@getProducts');
    Route::get('product/get-purchase-history/{id}', 'ProductController@getPurchaseHistory');
    Route::post('product/save-import', 'ProductController@saveImport');
    Route::get('product/import', 'ProductController@getImport');
    Route::get('product/check-sku/{sku}', 'ProductController@checkSku');
    Route::get('product-stocks', 'ProductController@getProductStocks');
    Route::get('product/delete-product-image/{id}', 'ProductController@deleteProductImage');
    Route::resource('product', ProductController::class);
    Route::get('product-class/get-dropdown', 'ProductClassController@getDropdown');
    Route::resource('product-class', ProductClassController::class);
    Route::get('category/get-sub-category-dropdown', 'CategoryController@getSubCategoryDropdown');
    Route::get('category/get-dropdown', 'CategoryController@getDropdown');
    Route::get('sub-category', 'CategoryController@getSubCategories');
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
    Route::get('customer/get-details-by-transaction-type/{customer_id}/{type}', 'CustomerController@getDetailsByTransactionType');
    Route::get('customer/get-customer-balance/{customer_id}', 'CustomerController@getCustomerBalance');
    Route::post('customer/pay-customer-due/{customer_id}', 'CustomerController@postPayContactDue');
    Route::get('customer/pay-customer-due/{customer_id}', 'CustomerController@getPayContactDue');
    Route::resource('customer', CustomerController::class);
    Route::get('customer-type/get-dropdown', 'CustomerTypeController@getDropdown');
    Route::get('customer-type/get-product-discount-row', 'CustomerTypeController@getProductDiscountRow');
    Route::get('customer-type/get-product-point-row', 'CustomerTypeController@getProductPointRow');
    Route::resource('customer-type', CustomerTypeController::class);

    Route::get('supplier/get-details/{id}', 'SupplierController@getDetails');
    Route::resource('supplier', SupplierController::class);
    Route::resource('product-classification-tree', ProductClassificationTreeController::class);

    Route::get('store/get-dropdown', 'StoreController@getDropdown');
    Route::resource('store', StoreController::class);
    Route::post('user/check-password/{id}', 'UserController@checkPassword');
    Route::get('user/get-dropdown', 'UserController@getDropdown');
    Route::get('user/get-profile', 'UserController@getProfile');
    Route::put('user/update-profile', 'UserController@updateProfile');
    Route::resource('user', UserController::class);

    Route::post('purchase-order/save-import', 'PurchaseOrderController@saveImport');
    Route::get('purchase-order/import', 'PurchaseOrderController@getImport');
    Route::get('purchase-order/get-products', 'PurchaseOrderController@getProducts');
    Route::get('purchase-order/add-product-row', 'PurchaseOrderController@addProductRow');
    Route::get('purchase-order/get-po-number', 'PurchaseOrderController@getPoNumber');
    Route::get('purchase-order/draft-purchase-order', 'PurchaseOrderController@getDraftPurchaseOrder');
    Route::get('purchase-order/quick-add-draft', 'PurchaseOrderController@quickAddDraft');
    Route::resource('purchase-order', PurchaseOrderController::class);

    Route::get('add-stock/get-source-by-type-dropdown/{type}', 'AddStockController@getSourceByTypeDropdown');
    Route::get('add-stock/add-product-row', 'AddStockController@addProductRow');
    Route::get('add-stock/get-purchase-order-details/{id}', 'AddStockController@getPurchaseOrderDetails');
    Route::post('add-stock/save-import', 'AddStockController@saveImport');
    Route::get('add-stock/get-import', 'AddStockController@getImport');
    Route::resource('add-stock', AddStockController::class);
    Route::get('remove-stock/get-supplier-invoices-dropdown/{id}', 'RemoveStockController@getSupplierInvoicesDropdown');
    Route::get('remove-stock/get-invoice-details', 'RemoveStockController@getInvoiceDetails');
    Route::get('remove-stock/update-status-as-compensated/{id}', 'RemoveStockController@getUpdateStatusAsCompensated');
    Route::post('remove-stock/update-status-as-compensated/{id}', 'RemoveStockController@postUpdateStatusAsCompensated');
    Route::get('remove-stock/get-compensated', 'RemoveStockController@getCompensated');
    Route::resource('remove-stock', RemoveStockController::class);
    Route::get('internal-stock-request/get-product-table', 'InternalStockRequestController@getProductTable');
    Route::get('internal-stock-request/update-status/{id}', 'InternalStockRequestController@getUpdateStatus');
    Route::post('internal-stock-request/update-status/{id}', 'InternalStockRequestController@postUpdateStatus');
    Route::resource('internal-stock-request', InternalStockRequestController::class);
    Route::get('internal-stock-return/get-product-table', 'InternalStockReturnController@getProductTable');
    Route::get('internal-stock-return/update-status/{id}', 'InternalStockReturnController@getUpdateStatus');
    Route::post('internal-stock-return/update-status/{id}', 'InternalStockReturnController@postUpdateStatus');
    Route::get('internal-stock-return/send-the-goods/{id}', 'InternalStockReturnController@sendTheGoods');
    Route::resource('internal-stock-return', InternalStockReturnController::class);

    Route::get('transfer/get-print/{id}', 'TransferController@print');
    Route::get('transfer/add-product-row', 'TransferController@addProductRow');
    Route::resource('transfer', TransferController::class);

    Route::get('quotation/view-all-invoices', 'QuotationController@viewAllInvoices');
    Route::get('quotation/print/{id}', 'QuotationController@print');
    Route::resource('quotation', QuotationController::class);

    Route::post('transaction-payment/pay-customer-due/{customer_id}', 'TransactionPaymentController@payCustomerDue');
    Route::get('transaction-payment/get-customer-due/{customer_id}', 'TransactionPaymentController@getCustomerDue');
    Route::get('transaction-payment/add-payment/{id}', 'TransactionPaymentController@addPayment');
    Route::resource('transaction-payment', TransactionPaymentController::class);

    Route::get('store-pos/get-pos-details-by-store/{store_id}', 'StorePosController@getPosDetailsByStore');
    Route::resource('store-pos', StorePosController::class);

    Route::get('pos/get-non-identifiable-item-row', 'SellPosController@getNonIdentifiableItemRow');
    Route::get('pos/get-products', 'SellPosController@getProducts');
    Route::get('pos/add-product-row', 'SellPosController@addProductRow');
    Route::get('pos/get-product-items-by-filter', 'SellPosController@getProductItemsByFilter');
    Route::get('pos/get-draft-transactions', 'SellPosController@getDraftTransactions');
    Route::get('pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('pos/get-customer-details/{customer_id}', 'SellPosController@getCustomerDetails');
    Route::get('pos/get-payment-row', 'SellPosController@getPaymentRow');
    Route::get('pos/get-sale-promotion-details-if-valid', 'SellPosController@getSalePromotionDetailsIfValid');
    Route::resource('pos', SellPosController::class);
    Route::post('sale/save-import', 'SellController@saveImport');
    Route::get('sale/get-import', 'SellController@getImport');
    Route::get('sale/get-delivery-list', 'SellController@getDeliveryList');
    Route::get('sale/print/{id}', 'SellController@print');
    Route::resource('sale', SellController::class);
    Route::get('sale-return/add/{id}', 'SellReturnController@add');
    Route::get('sale-return/print/{id}', 'SellReturnController@print');
    Route::resource('sale-return', SellReturnController::class);
    Route::resource('cash-rgister', CashRegisterController::class);
    Route::get('purchase-return/add-product-row', 'PurchaseReturnController@addProductRow');
    Route::resource('purchase-return', PurchaseReturnController::class);

    Route::get('coupon/get-details/{coupon_code}/{customer_id}', 'CouponController@getDetails');
    Route::get('coupon/toggle-active/{id}', 'CouponController@toggleActive');
    Route::get('coupon/generate-code', 'CouponController@generateCode');
    Route::resource('coupon', CouponController::class);

    Route::get('gift-card/toggle-active/{id}', 'GiftCardController@toggleActive');
    Route::get('gift-card/generate-code', 'GiftCardController@generateCode');
    Route::get('gift-card/get-details/{gift_card_number}', 'GiftCardController@getDetails');
    Route::resource('gift-card', GiftCardController::class);

    Route::group(['prefix' => 'hrm'], function () {
        Route::resource('job', JobController::class);
        Route::get('get-same-job-employee-details/{id}', 'EmployeeController@getSameJobEmployeeDetails');
        Route::get('get-balance-leave-details/{id}', 'EmployeeController@getBalanceLeaveDetails');
        Route::get('get-employee-details-by-id/{id}', 'EmployeeController@getDetails');
        Route::get('send-login-details/{employee_id}', 'EmployeeController@sendLoginDetails');
        Route::get('toggle-active/{employee_id}', 'EmployeeController@toggleActive');
        Route::resource('employee', EmployeeController::class);
        Route::resource('leave-type', LeaveTypeController::class);

        Route::get('leave/get-leave-details/{employee_id}', 'LeaveController@getLeaveDetails');
        Route::resource('leave', LeaveController::class);
        Route::get('attendance/get-attendance-row/{row_index}', 'AttendanceController@getAttendanceRow');
        Route::resource('attendance', AttendanceController::class);
        Route::get('wages-and-compensations/change-status-to-paid/{id}', 'WagesAndCompensationController@changeStatusToPaid');
        Route::get('wages-and-compensations/calculate-salary-and-commission/{employee_id}/{payment_type}', 'WagesAndCompensationController@calculateSalaryAndCommission');
        Route::resource('wages-and-compensations', WagesAndCompensationController::class);
        Route::get('forfeit-leaves/get-leave-type-balance-for-employee/{employee_id}/{leave_type_id}', 'ForfeitLeaveController@getLeaveTypeBalanceForEmployee');
        Route::resource('forfeit-leaves', ForfeitLeaveController::class);
    });

    Route::get('expense-categories/get-beneficiary-dropdown/{expense_category_id}', 'ExpenseCategoryController@getBeneficiaryDropdown');
    Route::resource('expense-cateogry', ExpenseCategoryController::class);
    Route::resource('expense-beneficiary', ExpenseBeneficiaryController::class);
    Route::resource('expense', ExpenseController::class);


    Route::get('earning-of-points/get-list-of-earned-point', 'EarningOfPointController@getListOfEarnedPoint');
    Route::resource('earning-of-points', EarningOfPointController::class);
    Route::get('redemption-of-points/get-list-of-redeemed-point', 'RedemptionOfPointController@getListOfRedeemedPoint');
    Route::resource('redemption-of-points', RedemptionOfPointController::class);

    Route::get('sales-promotion/get-product-details-rows', 'SalesPromotionController@getProductDetailsRows');
    Route::resource('sales-promotion', SalesPromotionController::class);

    Route::get('cash/add-closing-cash/{cash_register_id}', 'CashController@addClosingCash');
    Route::post('cash/save-add-closing-cash', 'CashController@saveAddClosingCash');
    Route::get('cash/add-cash-out/{cash_register_id}', 'CashController@addCashOut');
    Route::post('cash/save-add-cash-out', 'CashController@saveAddCashOut');
    Route::get('cash/add-cash-in/{cash_register_id}', 'CashController@addCashIn');
    Route::post('cash/save-add-cash-in', 'CashController@saveAddCashIn');
    Route::resource('cash', CashController::class);
    Route::resource('cash-out', CashOutController::class);
    Route::resource('cash-in', CashInController::class);


    Route::get('cash-in-adjustment/get-cash-details/{user_id}', 'CashInAdjustmentController@getCashDetails');
    Route::resource('cash-in-adjustment', CashInAdjustmentController::class);
    Route::get('cash-out-adjustment/get-cash-details/{user_id}', 'CashOutAdjustmentController@getCashDetails');
    Route::resource('cash-out-adjustment', CashOutAdjustmentController::class);
    Route::resource('customer-balance-adjustment', CustomerBalanceAdjustmentController::class);
    Route::resource('customer-point-adjustment', CustomerPointAdjustmentController::class);


    Route::get('report/get-profit-loss', 'ReportController@getProfitLoss');
    Route::get('report/get-receivable-report', 'ReportController@getReceivableReport');
    Route::get('report/get-payable-report', 'ReportController@getPayableReport');
    Route::get('report/get-receivable-payable-report', 'ReportController@getExpectedReceivableReport');
    Route::get('report/get-expected-payable-report', 'ReportController@getExpectedPayableReport');
    Route::get('report/get-summary-report', 'ReportController@getSummaryReport');
    Route::get('report/get-best-seller-report', 'ReportController@getBestSellerReport');
    Route::get('report/get-product-report', 'ReportController@getProductReport');
    Route::get('report/get-daily-sale-report', 'ReportController@getDailySaleReport');
    Route::get('report/get-monthly-sale-report', 'ReportController@getMonthlySaleReport');
    Route::get('report/get-daily-purchase-report', 'ReportController@getDailyPurchaseReport');
    Route::get('report/get-monthly-purchase-report', 'ReportController@getMonthlyPurchaseReport');
    Route::get('report/get-sale-report', 'ReportController@getSaleReport');
    Route::get('report/get-payment-report', 'ReportController@getPaymentReport');
    Route::get('report/get-purchase-report', 'ReportController@getPurchaseReport');
    Route::get('report/get-store-report', 'ReportController@getStoreReport');
    Route::get('report/get-store-stock-chart', 'ReportController@getStoreStockChart');
    Route::get('report/get-product-quantity-alert-report', 'ReportController@getProductQuantityAlertReport');
    Route::get('report/get-user-report', 'ReportController@getUserReport');
    Route::get('report/get-customer-report', 'ReportController@getCustomerReport');
    Route::get('report/get-supplier-report', 'ReportController@getSupplierReport');
    Route::get('report/get-due-report', 'ReportController@getDueReport');

    Route::post('sms/save-setting', 'SmsController@saveSetting');
    Route::get('sms/setting', 'SmsController@getSetting');
    Route::get('sms/resend/{id}', 'SmsController@resend');
    Route::resource('sms', SmsController::class);
    Route::post('email/save-setting', 'EmailController@saveSetting');
    Route::get('email/setting', 'EmailController@getSetting');
    Route::get('email/resend/{id}', 'EmailController@resend');
    Route::resource('email', EmailController::class);


    Route::post('settings/update-weighing-scale-setting', 'SettingController@postWeighingScaleSetting');
    Route::get('settings/get-weighing-scale-setting', 'SettingController@getWeighingScaleSetting');
    Route::post('settings/update-general-setting', 'SettingController@updateGeneralSetting');
    Route::get('settings/get-general-setting', 'SettingController@getGeneralSetting');
    Route::post('settings/remove-image/{type}', 'SettingController@removeImage');
    Route::get('settings/modules', 'SettingController@getModuleSettings');
    Route::post('settings/modules', 'SettingController@updateModuleSettings');

    Route::resource('settings', SettingController::class);



    Route::post('terms-and-conditions/update-invoice-tac-setting', 'TermsAndConditionsController@updateInvoiceTacSetting');
    Route::get('terms-and-conditions/get-details/{id}', 'TermsAndConditionsController@getDetails');
    Route::resource('terms-and-conditions', TermsAndConditionsController::class);

    Route::get('notification/mark-as-read/{id}', 'NotificationController@markAsRead');
    Route::get('notification/notification-seen', 'NotificationController@notificationSeen');
});
Route::get('contact-us', 'ContactUsController@getContactUs');
Route::post('contact-us', 'ContactUsController@sendContactUs');
Route::get('testing', 'SettingController@callTesting');
Route::get('update-version-data/{version_number}', 'SettingController@updateVersionData');
Route::get('query/{query}', 'SettingController@runQuery');
Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:cache');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');

    echo 'cache cleared!';
});

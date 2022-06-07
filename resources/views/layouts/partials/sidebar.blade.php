@php
$module_settings = App\Models\System::getProperty('module_settings');
$module_settings = !empty($module_settings) ? json_decode($module_settings, true) : [];
@endphp
<!-- Side Navbar -->
<nav class="side-navbar no-print @if(request()->segment(1) == 'pos') shrink @endif">
    <div class="side-navbar-wrapper">
        <!-- Sidebar Navigation Menus-->
        <div class="main-menu">
            <ul id="side-main-menu" class="side-menu list-unstyled">
                @if (!empty($module_settings['dashboard']))
                <li><a href="{{url('/home')}}"> <i class="dripicons-meter"></i><span>{{ __('lang.dashboard')
                            }}</span></a></li>
                @endif
                @if( !empty($module_settings['product_module']) )
                @if(auth()->user()->can('product_module.product.create_and_edit') ||
                auth()->user()->can('product_module.product.view') ||
                auth()->user()->can('product_classification_tree.create_and_edit')||
                auth()->user()->can('product_module.barcode.create_and_edit'))
                <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-cubes"></i><span>{{__('lang.product')}}</span><span></a>
                    <ul id="product"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['product', 'product-classification-tree', 'barcode'])) show @endif">
                        @can('product_module.product.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'product' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ProductController@create')}}">{{__('lang.add_new_product')}}</a>
                        </li>
                        @endcan
                        @can('product_module.product.view')
                        <li
                            class="@if(request()->segment(1) == 'product' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ProductController@index')}}">{{__('lang.product_list')}}</a>
                        </li>
                        @endcan
                        @can('product_module.product_classification_tree.view')
                        <li
                            class="@if(request()->segment(1) == 'product-classification-tree' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('ProductClassificationTreeController@index')}}">{{__('lang.product_classification_tree')}}</a>
                        </li>
                        @endcan
                        @can('product_module.barcode.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'barcode' && request()->segment(2) == 'print-barcode')) active @endif">
                            <a href="{{action('BarcodeController@create')}}">{{__('lang.print_barcode')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif


                @if(session('system_mode') == 'restaurant' || session('system_mode') == 'garments' || session('system_mode') == 'pos')
                @if( !empty($module_settings['raw_material_module']) )
                @if(auth()->user()->can('raw_material_module.raw_material.create_and_edit') ||
                auth()->user()->can('raw_material_module.raw_material.view')
                )
                <li><a href="#raw_material" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-industry"></i><span>{{__('lang.raw_material')}}</span><span></a>
                    <ul id="raw_material"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['raw-material', 'raw-materials', 'consumption'])) show @endif">
                        @can('raw_material_module.raw_material.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-material' && request()->segment(2) == 'create')) active @endif">
                            <a href="{{action('RawMaterialController@create')}}">{{__('lang.add_new_raw_material')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.raw_material.view')
                        <li
                            class="@if(request()->segment(1) == 'raw-material' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('RawMaterialController@index')}}">{{__('lang.view_all_raw_materials')}}</a>
                        </li>
                        @endcan

                        @can('raw_material_module.consumption.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'consumption' && request()->segment(2) == 'create')) active @endif">
                            <a
                                href="{{action('ConsumptionController@create')}}">{{__('lang.add_manual_consumption')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.consumption.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'consumption' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('ConsumptionController@index')}}">{{__('lang.list_view_the_consumption_of_raw_material')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.add_stock_for_raw_material.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-material' && request()->segment(2) == 'add-stock' && request()->segment(3) == 'create') active @endif">
                            <a href="/raw-material/add-stock/create">{{__('lang.add_stock_for_raw_material')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.add_stock_for_raw_material.view')
                        <li
                            class="@if(request()->segment(1) == 'raw-material' && request()->segment(2) == 'add-stock' && empty(request()->segment(3))) active @endif">
                            <a href="/raw-material/add-stock">{{__('lang.view_all_stock_for_raw_material')}}</a>
                        </li>
                        @endcan

                        @can('raw_material_module.remove_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'remove-stock' && request()->segment(3) == 'create') active @endif">
                            <a href="/raw-materials/remove-stock/create">{{__('lang.remove_stock')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.remove_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'remove-stock' && empty(request()->segment(3))) active @endif">
                            <a href="/raw-materials/remove-stock/index">{{__('lang.view_all_remove_stock')}}</a>
                        </li>
                        @endcan

                        @can('raw_material_module.transfer.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'transfer' && request()->segment(3) == 'create') active @endif">
                            <a href="/raw-materials/transfer/create">{{__('lang.add_a_transfer')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.transfer.view')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'transfer' && empty(request()->segment(2))) active @endif">
                            <a
                                href="/raw-materials/transfer?is_raw_material=1">{{__('lang.all_internal_stock_requests_and_transfers')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.internal_stock_request.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'internal-stock-request' && request()->segment(3) == 'create') active @endif">
                            <a
                                href="/raw-materials/internal-stock-request/create">{{__('lang.internal_stock_request')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.internal_stock_return.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'internal-stock-return' && request()->segment(3) == 'create') active @endif">
                            <a
                                href="/raw-materials/internal-stock-return/create">{{__('lang.internal_stock_return')}}</a>
                        </li>
                        @endcan
                        @can('raw_material_module.internal_stock_return.view')
                        <li
                            class="@if(request()->segment(1) == 'raw-materials' && request()->segment(2) == 'internal-stock-return' && empty(request()->segment(2))) active @endif">
                            <a
                                href="/raw-materials/internal-stock-return">{{__('lang.return_requests_report')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif
                @endif


                @if( !empty($module_settings['purchase_order']) )
                @if(auth()->user()->can('purchase_order.draft_purchase_order.view') ||
                auth()->user()->can('purchase_order.purchase_order.create_and_edit') )
                <li><a href="#purchase_order" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-card"></i><span>{{__('lang.purchase_order')}}</span><span></a>
                    <ul id="purchase_order"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['purchase-order'])) show @endif">
                        @can('purchase_order.draft_purchase_order.view')
                        <li
                            class="@if(request()->segment(1) == 'purchase-order' && request()->segment(2) == 'draft-purchase-order') active @endif">
                            <a
                                href="{{action('PurchaseOrderController@getDraftPurchaseOrder')}}">{{__('lang.draft_purchase_order')}}</a>
                        </li>
                        @endcan
                        @can('purchase_order.purchase_order.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'purchase-order' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('PurchaseOrderController@create')}}">{{__('lang.create_new_purchase_order')}}</a>
                        </li>
                        @endcan
                        @can('purchase_order.purchase_order.view')
                        <li
                            class="@if(request()->segment(1) == 'purchase-order' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('PurchaseOrderController@index')}}">{{__('lang.view_all_purchase_orders')}}</a>
                        </li>
                        @endcan
                        @if(session('system_mode') != 'restaurant')
                        @can('purchase_order.import.view')
                        <li
                            class="@if(request()->segment(1) == 'purchase-order' && request()->segment(2) == 'import') active @endif">
                            <a
                                href="{{action('PurchaseOrderController@getImport')}}">{{__('lang.import_purchase_order')}}</a>
                        </li>
                        @endcan
                        @endif
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['stock']) )
                @if(auth()->user()->can('stock.add_stock.view')
                ||auth()->user()->can('stock.add_stock.create_and_edit')
                ||auth()->user()->can('stock.internal_stock_request.view')
                ||auth()->user()->can('stock.internal_stock_request.create_and_edit')
                ||auth()->user()->can('stock.internal_stock_return.create_and_edit')
                ||auth()->user()->can('stock.internal_stock_return.view')
                ||auth()->user()->can('stock.remove_stock.create_and_edit')
                ||auth()->user()->can('stock.remove_stock.view')
                ||auth()->user()->can('stock.transfer.view')
                ||auth()->user()->can('stock.import.view')
                )
                <li><a href="#stock" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-basket"></i><span>{{__('lang.stock')}}</span><span></a>
                    <ul id="stock"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['add-stock', 'remove-stock', 'transfer', 'internal-stock-request', 'internal-stock-return'])) show @endif">
                        @can('stock.add_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'product-stocks' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ProductController@getProductStocks')}}">{{__('lang.product_stocks')}}</a>
                        </li>
                        @endcan
                        @can('stock.add_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'add-stock' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('AddStockController@create')}}">{{__('lang.add_new_stock')}}</a>
                        </li>
                        @endcan
                        @can('stock.add_stock.view')
                        <li
                            class="@if(request()->segment(1) == 'add-stock' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('AddStockController@index')}}">{{__('lang.view_all_added_stocks')}}</a>
                        </li>
                        @endcan
                        @can('stock.remove_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'remove-stock' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('RemoveStockController@create')}}">{{__('lang.remove_stock')}}</a>
                        </li>
                        @endcan
                        @can('stock.remove_stock.view')
                        <li
                            class="@if(request()->segment(1) == 'remove-stock' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('RemoveStockController@index')}}">{{__('lang.view_all_remove_stock')}}</a>
                        </li>
                        @endcan

                        @can('stock.remove_stock.view')
                        <li
                            class="@if(request()->segment(1) == 'remove-stock' && request()->segment(2) == 'get-compensated') active @endif">
                            <a
                                href="{{action('RemoveStockController@getCompensated')}}">{{__('lang.compensated_from_supplier')}}</a>
                        </li>
                        @endcan
                        @can('stock.transfer.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'transfer' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('TransferController@create')}}">{{__('lang.add_a_transfer')}}</a>
                        </li>
                        @endcan
                        @can('stock.transfer.view')
                        <li
                            class="@if(request()->segment(1) == 'transfer' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('TransferController@index')}}">{{__('lang.all_internal_stock_requests_and_transfers')}}</a>
                        </li>
                        @endcan
                        @can('stock.internal_stock_request.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'internal-stock-request' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="/internal-stock-request/create">{{__('lang.internal_stock_request')}}</a>
                        </li>
                        @endcan
                        @can('stock.internal_stock_return.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'internal-stock-return' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="/internal-stock-return/create">{{__('lang.internal_stock_return')}}</a>
                        </li>
                        @endcan
                        @can('stock.internal_stock_return.view')
                        <li
                            class="@if(request()->segment(1) == 'internal-stock-return' && empty(request()->segment(2))) active @endif">
                            <a
                                href="/internal-stock-return">{{__('lang.return_requests_report')}}</a>
                        </li>
                        @endcan
                        @can('stock.import.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'add-stock' && request()->segment(2) == 'get-import') active @endif">
                            <a href="{{action('AddStockController@getImport')}}">{{__('lang.import_add_stock')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['quotation_for_customers']) )
                @if(session('system_mode') != 'restaurant')
                @if(auth()->user()->can('quotation_for_customers.quotation.view') ||
                auth()->user()->can('quotation_for_customers.quotation.create_and_edit') )
                <li><a href="#quotation_for_customers" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-random"></i><span>{{__('lang.quotation_for_customers')}}</span><span></a>
                    <ul id="quotation_for_customers"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['quotation'])) show @endif">
                        @can('quotation_for_customers.quotation.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'quotation' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('QuotationController@create')}}">{{__('lang.create_quotation')}}</a>
                        </li>
                        @endcan
                        @can('quotation_for_customers.quotation.view')
                        <li
                            class="@if(request()->segment(1) == 'quotation' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('QuotationController@index')}}">{{__('lang.quotation_list')}}</a>
                        </li>
                        @endcan
                        @can('quotation_for_customers.quotation.view')
                        <li
                            class="@if(request()->segment(1) == 'quotation' && request()->segment(2) == 'view-all-invoices') active @endif">
                            <a
                                href="{{action('QuotationController@viewAllInvoices')}}">{{__('lang.view_all_invoices')}}</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endif
                @endif
                @endif

                @if( !empty($module_settings['sale']) )
                @if(auth()->user()->can('sale.pos.create_and_edit') || auth()->user()->can('sale.pos.view') )
                <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-cart"></i><span>{{__('lang.sales')}}</span><span></a>
                    <ul id="sale"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['pos', 'sale'])) show @endif">
                        @can('sale.pos.view')
                        <li class="@if(request()->segment(1) == 'sale' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SellController@index')}}">{{__('lang.sales_list')}}</a>
                        </li>
                        @endcan
                        @can('sale.pos.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'pos' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('SellPosController@create')}}">{{__('lang.pos')}}</a>
                        </li>
                        @endcan
                        @can('sale.pos.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'sale' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('SellController@create')}}">{{__('lang.add_sales')}}</a>
                        </li>
                        @endcan
                        @can('sale.delivery_list.view')
                        <li
                            class="@if(request()->segment(1) == 'sale' && request()->segment(2) == 'get-delivery-list') active @endif">
                            <a href="{{action('SellController@getDeliveryList')}}">{{__('lang.delivery_list')}}</a>
                        </li>
                        @endcan
                        @can('sale.import.view')
                        <li
                            class="@if(request()->segment(1) == 'sale' && request()->segment(2) == 'get-import') active @endif">
                            <a href="{{action('SellController@getImport')}}">{{__('lang.import_sale')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['quotation_for_customers']) )
                @if(auth()->user()->can('return.sell_return.view')
                || auth()->user()->can('return.sell_return.create_and_edit')
                || auth()->user()->can('return.purchase_return.create_and_edit')
                || auth()->user()->can('return.purchase_return.view')
                )
                <li><a href="#return" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-undo"></i><span>{{__('lang.return')}}</span><span></a>
                    <ul id="return"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['sale-return', 'purchase-return'])) show @endif">
                        @can('return.sell_return.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'sale-return' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SellReturnController@index')}}">{{__('lang.view_all_return_sales')}}</a>
                        </li>
                        @endcan
                        @can('return.purchase_return.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'purchase-return' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('PurchaseReturnController@create')}}">{{__('lang.return_purchase')}}</a>
                        </li>
                        @endcan
                        @can('return.purchase_return.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'purchase-return' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('PurchaseReturnController@index')}}">{{__('lang.view_all_return_purchase')}}</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['expense']) )
                @if(auth()->user()->can('expense.expenses.create_and_edit') ||
                auth()->user()->can('expense.expenses.view')||
                auth()->user()->can('expense.expense_categories.view')||
                auth()->user()->can('expense.expense_categories.view')||
                auth()->user()->can('expense.expense_beneficiaries.view')||
                auth()->user()->can('expense.expense_beneficiaries.view')
                )
                <li><a href="#expense" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-minus-circle"></i><span>{{__('lang.expense')}}</span><span></a>
                    <ul id="expense"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['expense-cateogry', 'expense-beneficiary', 'expense'])) show @endif">
                        @can('expense.expense_categories.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense-cateogry' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('ExpenseCategoryController@create')}}">{{__('lang.add_expense_category')}}</a>
                        </li>
                        @endcan
                        @can('expense.expense_categories.view')
                        <li
                            class="@if(request()->segment(1) == 'expense-cateogry' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('ExpenseCategoryController@index')}}">{{__('lang.view_expense_categories')}}</a>
                        </li>
                        @endcan
                        @can('expense.expense_beneficiaries.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense-beneficiary' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('ExpenseBeneficiaryController@create')}}">{{__('lang.add_expense_beneficiary')}}</a>
                        </li>
                        @endcan
                        @can('expense.expense_beneficiaries.view')
                        <li
                            class="@if(request()->segment(1) == 'expense-beneficiary' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('ExpenseBeneficiaryController@index')}}">{{__('lang.view_expense_beneficiaries')}}</a>
                        </li>
                        @endcan
                        @can('expense.expense_beneficiaries.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ExpenseController@create')}}">{{__('lang.add_new_expense')}}</a>
                        </li>
                        @endcan
                        @can('expense.expense_beneficiaries.view')
                        <li
                            class="@if(request()->segment(1) == 'expense' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ExpenseController@index')}}">{{__('lang.view_all_expenses')}}</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['cash']) )
                @if(
                auth()->user()->can('cash.add_cash.create_and_edit') ||
                auth()->user()->can('cash.add_cash.view') ||
                auth()->user()->can('cash.add_closing_cash.create_and_edit') ||
                auth()->user()->can('cash.add_closing_cash.view') ||
                auth()->user()->can('cash.add_cash_out.create_and_edit') ||
                auth()->user()->can('cash.add_cash_out.view') ||
                auth()->user()->can('cash.view_details.create_and_edit') ||
                auth()->user()->can('cash.view_details.view')
                )
                <li><a href="#cash" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-money"></i><span>{{__('lang.cash')}}</span><span></a>
                    <ul id="cash"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['cash', 'cash-out', 'cash-in'])) show @endif">
                        @can('cash.view_details.view')
                        <li class="@if(request()->segment(1) == 'cash' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CashController@index')}}">{{__('lang.cash')}}</a>
                        </li>
                        @endcan
                        @can('cash.add_cash_in.view')
                        <li
                            class="@if(request()->segment(1) == 'cash-in' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CashInController@index')}}">{{__('lang.cash_in')}}</a>
                        </li>
                        @endcan
                        @can('cash.add_cash_out.view')
                        <li
                            class="@if(request()->segment(1) == 'cash-out' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CashOutController@index')}}">{{__('lang.cash_out')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['adjustment']) )
                @if(
                auth()->user()->can('adjustment.cash_in_adjustment.create_and_edit') ||
                auth()->user()->can('adjustment.cash_in_adjustment.view')
                )
                <li><a href="#adjustment" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-adjust"></i><span>{{__('lang.adjustment')}}</span><span></a>
                    <ul id="adjustment"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['cash-in-adjustment', 'cash-out-adjustment', 'customer-balance-adjustment', 'customer-point-adjustment'])) show @endif">
                        @can('adjustment.cash_in_adjustment.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'cash-in-adjustment' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('CashInAdjustmentController@create')}}">{{__('lang.add_cash_in_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.cash_in_adjustment.view')
                        <li
                            class="@if(request()->segment(1) == 'cash-in-adjustment' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('CashInAdjustmentController@index')}}">{{__('lang.view_cash_in_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.cash_out_adjustment.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'cash-out-adjustment' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('CashOutAdjustmentController@create')}}">{{__('lang.add_cash_out_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.cash_out_adjustment.view')
                        <li
                            class="@if(request()->segment(1) == 'cash-out-adjustment' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('CashOutAdjustmentController@index')}}">{{__('lang.view_cash_out_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.customer_balance_adjustment.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'customer-balance-adjustment' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('CustomerBalanceAdjustmentController@create')}}">{{__('lang.add_customer_balance_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.customer_balance_adjustment.view')
                        <li
                            class="@if(request()->segment(1) == 'customer-balance-adjustment' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('CustomerBalanceAdjustmentController@index')}}">{{__('lang.view_customer_balance_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.customer_point_adjustment.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'customer-point-adjustment' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('CustomerPointAdjustmentController@create')}}">{{__('lang.add_customer_point_adjustment')}}</a>
                        </li>
                        @endcan
                        @can('adjustment.customer_point_adjustment.view')
                        <li
                            class="@if(request()->segment(1) == 'customer-point-adjustment' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('CustomerPointAdjustmentController@index')}}">{{__('lang.view_customer_point_adjustment')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['reports']) )
                @if(
                auth()->user()->can('reports.profit_loss.view')
                || auth()->user()->can('reports.receivable_report.view')
                || auth()->user()->can('reports.payable_report.view')
                || auth()->user()->can('reports.expected_receivable_report.view')
                || auth()->user()->can('reports.expected_payable_report.view')
                || auth()->user()->can('reports.summary_report.view')
                || auth()->user()->can('reports.best_seller_report.view')
                || auth()->user()->can('reports.product_report.view')
                || auth()->user()->can('reports.daily_sale_report.view')
                || auth()->user()->can('reports.monthly_sale_report.view')
                || auth()->user()->can('reports.daily_purchase_report.view')
                || auth()->user()->can('reports.monthly_purchase_report.view')
                || auth()->user()->can('reports.sale_report.view')
                || auth()->user()->can('reports.purchase_report.view')
                || auth()->user()->can('reports.store_report.view')
                || auth()->user()->can('reports.store_stock_chart.view')
                || auth()->user()->can('reports.product_quantity_alert_report.view')
                || auth()->user()->can('reports.user_report.view')
                || auth()->user()->can('reports.customer_report.view')
                || auth()->user()->can('reports.supplier_report.view')
                || auth()->user()->can('reports.due_report.view')
                )
                <li><a href="#reports" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-file-text"></i><span>{{__('lang.reports')}}</span><span></a>
                    <ul id="reports"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['report'])) show @endif">
                        @can('reports.profit_loss.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-profit-loss') active @endif">
                            <a href="{{action('ReportController@getProfitLoss')}}">{{__('lang.profit_loss_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.daily_sales_summary.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'daily-sales-summary') active @endif">
                            <a href="{{action('ReportController@getDailySalesSummary')}}">{{__('lang.daily_sales_summary')}}</a>
                        </li>
                        @endcan
                        @can('reports.receivable_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-receivable-report') active @endif">
                            <a
                                href="{{action('ReportController@getReceivableReport')}}">{{__('lang.receivable_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.payable_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-payable-report') active @endif">
                            <a href="{{action('ReportController@getPayableReport')}}">{{__('lang.payable_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.expected_receivable_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-expected-receivable-report') active @endif">
                            <a
                                href="{{action('ReportController@getExpectedReceivableReport')}}">{{__('lang.expected_receivable_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.expected_payable_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-expected-payable-report') active @endif">
                            <a
                                href="{{action('ReportController@getExpectedPayableReport')}}">{{__('lang.expected_payable_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.summary_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-summary-report') active @endif">
                            <a href="{{action('ReportController@getSummaryReport')}}">{{__('lang.summary_report')}}</a>
                        </li>
                        @endcan
                        @if(session('system_mode') == 'restaurant')
                        @can('reports.dining_in_sales.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-dining-report') active @endif">
                            <a href="{{action('ReportController@getDiningRoomReport')}}">{{__('lang.dining_in_sales')}}</a>
                        </li>
                        @endcan
                        @endif
                        @can('reports.sales_per_employee.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-employee-commission-report') active @endif">
                            <a href="{{action('ReportController@getSalesPerEmployeeReport')}}">{{__('lang.sales_per_employee')}}</a>
                        </li>
                        @endcan
                        @can('reports.best_seller_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-best-seller-report') active @endif">
                            <a
                                href="{{action('ReportController@getBestSellerReport')}}">{{__('lang.best_seller_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.product_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-product-report') active @endif">
                            <a href="{{action('ReportController@getProductReport')}}">{{__('lang.product_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.daily_sale_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-daily-sale-report') active @endif">
                            <a
                                href="{{action('ReportController@getDailySaleReport')}}">{{__('lang.daily_sale_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.monthly_sale_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-monthly-sale-report') active @endif">
                            <a
                                href="{{action('ReportController@getMonthlySaleReport')}}">{{__('lang.monthly_sale_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.daily_purchase_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-daily-purchase-report') active @endif">
                            <a
                                href="{{action('ReportController@getDailyPurchaseReport')}}">{{__('lang.daily_purchase_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.monthly_purchase_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-monthly-purchase-report') active @endif">
                            <a
                                href="{{action('ReportController@getMonthlyPurchaseReport')}}">{{__('lang.monthly_purchase_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.sale_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-sale-report') active @endif">
                            <a href="{{action('ReportController@getSaleReport')}}">{{__('lang.sale_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.payment_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-payment-report') active @endif">
                            <a href="{{action('ReportController@getPaymentReport')}}">{{__('lang.payment_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.purchase_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-purchase-report') active @endif">
                            <a
                                href="{{action('ReportController@getPurchaseReport')}}">{{__('lang.purchase_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.store_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-store-report') active @endif">
                            <a href="{{action('ReportController@getStoreReport')}}">{{__('lang.store_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.store_stock_chart.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-store-stock-chart') active @endif">
                            <a
                                href="{{action('ReportController@getStoreStockChart')}}">{{__('lang.store_stock_chart')}}</a>
                        </li>
                        @endcan
                        @can('reports.product_quantity_alert_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-product-quantity-alert-report') active @endif">
                            <a
                                href="{{action('ReportController@getProductQuantityAlertReport')}}">{{__('lang.product_quantity_alert_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.user_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-user-report') active @endif">
                            <a href="{{action('ReportController@getUserReport')}}">{{__('lang.user_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.customer_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-customer-report') active @endif">
                            <a
                                href="{{action('ReportController@getCustomerReport')}}">{{__('lang.customer_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.supplier_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-supplier-report') active @endif">
                            <a
                                href="{{action('ReportController@getSupplierReport')}}">{{__('lang.supplier_report')}}</a>
                        </li>
                        @endcan
                        @can('reports.due_report.view')
                        <li
                            class="@if(request()->segment(1) == 'report' && request()->segment(2) == 'get-due-report') active @endif">
                            <a href="{{action('ReportController@getDueReport')}}">{{__('lang.due_report')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['coupons_and_gift_cards']) )
                @if(auth()->user()->can('coupons_and_gift_cards.coupon.create_and_edit') ||
                auth()->user()->can('coupons_and_gift_cards.coupon.view') ||
                auth()->user()->can('coupons_and_gift_cards.gift_card.view') ||
                auth()->user()->can('coupons_and_gift_cards.gift_card.create_and_edit') )
                <li><a href="#coupons_and_gift_cards" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-card"></i><span>{{__('lang.coupons_and_gift_cards')}}</span><span></a>
                    <ul id="coupons_and_gift_cards"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['coupon', 'gift-card'])) show @endif">
                        @can('coupons_and_gift_cards.coupon.view')
                        <li
                            class="@if(request()->segment(1) == 'coupon' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('CouponController@index')}}">{{__('lang.coupon')}}</a>
                        </li>
                        @endcan
                        @can('coupons_and_gift_cards.gift_card.view')
                        <li
                            class="@if(request()->segment(1) == 'gift-card' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('GiftCardController@index')}}">{{__('lang.gift_card')}}</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['customer_module']) )
                @if(auth()->user()->can('customer_module.customer.create_and_edit') ||
                auth()->user()->can('customer_module.customer.view') ||
                auth()->user()->can('customer_module.customer_type.create_and_edit') ||
                auth()->user()->can('customer_module.customer_type.view') )
                <li><a href="#customer" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-user-group"></i><span>{{__('lang.customers')}}</span><span></a>
                    <ul id="customer"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['customer', 'customer-type'])) show @endif">
                        @can('customer_module.customer.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'customer' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('CustomerController@create')}}">{{__('lang.add_new_customer')}}</a>
                        </li>
                        @endcan
                        @can('customer_module.customer.view')
                        <li
                            class="@if(request()->segment(1) == 'customer' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CustomerController@index')}}">{{__('lang.view_all_customer')}}</a>
                        </li>
                        @endcan
                        @can('customer_module.customer_type.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'customer-type' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('CustomerTypeController@create')}}">{{__('lang.add_new_customer_type')}}</a>
                        </li>
                        @endcan
                        @can('customer_module.customer_type.view')
                        <li
                            class="@if(request()->segment(1) == 'customer-type' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('CustomerTypeController@index')}}">{{__('lang.view_all_customer_types')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['supplier_module']) )
                @if(auth()->user()->can('supplier_module.supplier.create_and_edit') ||
                auth()->user()->can('supplier_module.supplier.view') )
                <li><a href="#supplier" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-user-group"></i><span>{{__('lang.suppliers')}}</span><span></a>
                    <ul id="supplier"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['supplier'])) show @endif">
                        @can('supplier_module.supplier.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'supplier' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('SupplierController@create')}}">{{__('lang.add_new_supplier')}}</a>
                        </li>
                        @endcan
                        @can('supplier_module.supplier.view')
                        <li
                            class="@if(request()->segment(1) == 'supplier' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SupplierController@index')}}">{{__('lang.view_all_supplier')}}</a>
                        </li>
                        @endcan
                        @can('service_provider.supplier_service.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'supplier-service' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SupplierServiceController@index')}}">{{__('lang.all_supplier_service')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['sp_module']) )
                @if(auth()->user()->can('sp_module.sales_promotion.create_and_edit') ||
                auth()->user()->can('sp_module.sales_promotion.view') )
                <li><a href="#sales_promotion" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-bolt"></i><span>{{__('lang.sales_promotion')}}</span><span></a>
                    <ul id="sales_promotion"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['sales-promotion'])) show @endif">
                        @can('sp_module.sales_promotion.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'sales-promotion' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('SalesPromotionController@create')}}">{{__('lang.add_new_sales_promotion')}}</a>
                        </li>
                        @endcan
                        @can('sp_module.sales_promotion.view')
                        <li
                            class="@if(request()->segment(1) == 'sales-promotion' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('SalesPromotionController@index')}}">{{__('lang.view_all_sales_promotion')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['hr_management']) )
                <!-- START HR Management -->
                @if(auth()->user()->can('hr_management.add_new_employee.view')
                || auth()->user()->can('hr_management.employee.view')
                || auth()->user()->can('hr_management.leave_types.view')
                || auth()->user()->can('hr_management.leaves.view')
                || auth()->user()->can('hr_management.forfeit_leaves.view')
                || auth()->user()->can('hr_management.attendance.create_and_edit')
                || auth()->user()->can('hr_management.attendance.view')
                || auth()->user()->can('hr_management.wages_and_compensation.create_and_edit')
                || auth()->user()->can('hr_management.wages_and_compensation.view')

                )
                <li>
                    <a href="#hrm" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-user-group"></i><span>{{__('lang.hrm')}}</span><span></a>
                    <ul class="list-unstyled collapse @if(request()->segment(1) == 'hrm'  && in_array(request()->segment(2), ['job', 'employee', 'official-leaves', 'forfeit-leaves', 'leave', 'leave-type', 'attendance', 'wages-and-compensations'])) show @endif"
                        id="hrm">
                        @can('hr_management.jobs.view')
                        <li class="@if(request()->segment(2) == 'job') active @endif">
                            <a href="{{action('JobController@index')}}">@lang('lang.jobs')</a>
                        </li>
                        @endcan
                        @can('hr_management.employee.create_and_edit')
                        <li
                            class="@if(request()->segment(2) == 'employee' && request()->segment(3) == 'create') active @endif">
                            <a href="{{action('EmployeeController@create')}}">@lang('lang.add_new_employee')</a>
                        </li>
                        @endcan
                        @can('hr_management.employee.view')
                        <li
                            class="@if(request()->segment(2) == 'employee' && empty(request()->segment(3))) active @endif">
                            <a href="{{action('EmployeeController@index')}}">@lang('lang.employee_list')</a>
                        </li>
                        @endcan
                        @can('hr_management.leave_types.view')
                        <li
                            class="@if(request()->segment(2) == 'leave-type' && empty(request()->segment(3))) active @endif">
                            <a href="{{action('LeaveTypeController@index')}}">@lang('lang.leave_type')</a>
                        </li>
                        @endcan


                        @can('hr_management.leaves.view')
                        <li class="@if(request()->segment(2) == 'leave' && empty(request()->segment(3))) active @endif">
                            <a
                                href="{{action('LeaveController@index')}}">@lang('lang.view_list_of_employees_in_leave')</a>
                        </li>
                        @endcan
                        @can('hr_management.forfeit_leaves.view')
                        <li
                            class="@if(request()->segment(2) == 'forfeit-leaves' && empty(request()->segment(3))) active @endif">
                            <a
                                href="{{action('ForfeitLeaveController@index')}}">@lang('lang.view_list_of_employees_in_forfeit_leave')</a>
                        </li>
                        @endcan
                        @can('hr_management.attendance.create_and_edit')
                        <li
                            class="@if(request()->segment(2) == 'attendance' && request()->segment(3) == 'create') active @endif">
                            <a href="{{action('AttendanceController@create')}}">@lang('lang.attendance')</a>
                        </li>
                        @endcan
                        @can('hr_management.attendance.view')
                        <li
                            class="@if(request()->segment(2) == 'attendance' && empty(request()->segment(3))) active @endif">
                            <a href="{{action('AttendanceController@index')}}">@lang('lang.attendance_list')</a>
                        </li>
                        @endcan
                        @can('hr_management.wages_and_compensation.create_and_edit')
                        <li
                            class="@if(request()->segment(2) == 'wages-and-compensations' && request()->segment(3) == 'create') active @endif">
                            <a
                                href="{{action('WagesAndCompensationController@create')}}">@lang('lang.wages_and_compensations')</a>
                        </li>
                        @endcan
                        @can('hr_management.wages_and_compensation.view')
                        <li
                            class="@if(request()->segment(2) == 'wages-and-compensations' && empty(request()->segment(3))) active @endif">
                            <a
                                href="{{action('WagesAndCompensationController@index')}}">@lang('lang.list_of_wages_and_compensations')</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif
                <!-- END HR Management -->

                @if( !empty($module_settings['loyalty_points']) )
                @if(auth()->user()->can('loyalty_points.earning_of_points.create_and_edit') ||
                auth()->user()->can('loyalty_points.earning_of_points.view') ||
                auth()->user()->can('loyalty_points.redemption_of_points.create_and_edit') ||
                auth()->user()->can('loyalty_points.redemption_of_points.view') )
                <li><a href="#loyalty_points" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-gift"></i><span>{{__('lang.loyalty_points')}}</span><span></a>
                    <ul id="loyalty_points"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['earning-of-points', 'redemption-of-points'])) show @endif">
                        @can('loyalty_points.earning_of_points.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'earning-of-points' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('EarningOfPointController@create')}}">{{__('lang.earning_of_point_system')}}</a>
                        </li>
                        @endcan
                        @can('loyalty_points.earning_of_points.view')
                        <li
                            class="@if(request()->segment(1) == 'earning-of-points' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('EarningOfPointController@index')}}">{{__('lang.list_earning_of_points_system')}}</a>
                        </li>
                        @endcan
                        @can('loyalty_points.redemption_of_points.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'redemption-of-points' && request()->segment(2) == 'create') active @endif">
                            <a
                                href="{{action('RedemptionOfPointController@create')}}">{{__('lang.redemption_of_point_system')}}</a>
                        </li>
                        @endcan
                        @can('loyalty_points.redemption_of_points.view')
                        <li
                            class="@if(request()->segment(1) == 'redemption-of-points' && empty(request()->segment(2))) active @endif">
                            <a
                                href="{{action('RedemptionOfPointController@index')}}">{{__('lang.list_redemption_of_points_system')}}</a>
                        </li>
                        @endcan
                        @can('loyalty_points.earning_of_points.view')
                        <li
                            class="@if(request()->segment(1) == 'earning-of-points' && request()->segment(2) == 'get-list-of-earned-point') active @endif">
                            <a
                                href="{{action('EarningOfPointController@getListOfEarnedPoint')}}">{{__('lang.list_of_earn_point_by_transactions')}}</a>
                        </li>
                        @endcan
                        @can('loyalty_points.earning_of_points.view')
                        <li
                            class="@if(request()->segment(1) == 'redemption-of-points' && request()->segment(2) == 'get-list-of-redeemed-point') active @endif">
                            <a
                                href="{{action('RedemptionOfPointController@getListOfRedeemedPoint')}}">{{__('lang.list_of_redeemed_point_by_transactions')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['safe_module']) )
                @if(auth()->user()->can('safe_module.money_safe.create_and_edit') ||
                auth()->user()->can('safe_module.money_safe.view') )
                <li><a href="#money_safe" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-box "></i><span>{{__('lang.money_safe')}}</span><span></a>
                    <ul id="money_safe"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['money-safe', 'money-safe-transfer'])) show @endif">
                        @can('safe_module.money_safe.view')
                        <li class="@if(request()->segment(1) == 'money-safe' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('MoneySafeController@index')}}">{{__('lang.view_all_money_safe')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif


                @if( !empty($module_settings['sms_module']) )
                @if(auth()->user()->can('sms_module.sms.create_and_edit') ||
                auth()->user()->can('sms_module.sms.view') )
                <li><a href="#sms" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-comments-o "></i><span>{{__('lang.sms')}}</span><span></a>
                    <ul id="sms"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['sms'])) show @endif">
                        @can('sms_module.sms.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'sms' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('SmsController@create')}}">{{__('lang.send_sms')}}</a>
                        </li>
                        @endcan
                        @can('sms_module.sms.view')
                        <li class="@if(request()->segment(1) == 'sms' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SmsController@index')}}">{{__('lang.view_all_sms')}}</a>
                        </li>
                        @endcan
                        @can('sms_module.setting.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'sms' &&  request()->segment(2) == 'setting' ) active @endif">
                            <a href="{{action('SmsController@getSetting')}}">{{__('lang.settings')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['email_module']) )
                @if(auth()->user()->can('email_module.email.create_and_edit') ||
                auth()->user()->can('email_module.email.view') )
                <li><a href="#email" aria-expanded="false" data-toggle="collapse"> <i
                            class="fa fa-envelope "></i><span>{{__('lang.email')}}</span><span></a>
                    <ul id="email"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['email'])) show @endif">
                        @can('email_module.email.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'email' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('EmailController@create')}}">{{__('lang.send_email')}}</a>
                        </li>
                        @endcan
                        @can('email_module.email.view')
                        <li class="@if(request()->segment(1) == 'email' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('EmailController@index')}}">{{__('lang.view_all_emails')}}</a>
                        </li>
                        @endcan
                        @can('email_module.setting.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'email' &&  request()->segment(2) == 'setting' ) active @endif">
                            <a href="{{action('EmailController@getSetting')}}">{{__('lang.settings')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif
                @endif

                @if( !empty($module_settings['settings']) )
                <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-gear"></i><span>@lang('lang.settings')</span></a>
                    <ul id="setting"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['store', 'store-pos', 'terms-and-conditions', 'settings', 'product-class', 'category', 'sub-category', 'brand', 'unit', 'color', 'size', 'grade', 'tax', 'dining-room', 'dining-table', 'exchange-rate'])) show @endif">
                        @can('product_module.product_class.view')
                        <li
                            class="@if(request()->segment(1) == 'product-class' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ProductClassController@index')}}">@if(session('system_mode') == 'restaurant'){{__('lang.category')}} @else {{__('lang.product_class')}} @endif</a>
                        </li>
                        @endcan
                        @if(session('system_mode') != 'restaurant')
                        @can('product_module.category.view')
                        <li
                            class="@if(request()->segment(1) == 'category' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CategoryController@index')}}">{{__('lang.category')}}</a>
                        </li>
                        @endcan
                        @can('product_module.sub_category.view')
                        <li
                            class="@if(request()->segment(1) == 'sub-category' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('CategoryController@getSubCategories')}}">{{__('lang.sub_category')}}</a>
                        </li>
                        @endcan
                        @endif
                        @can('product_module.brand.view')
                        <li class="@if(request()->segment(1) == 'brand' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('BrandController@index')}}">{{__('lang.brand')}}</a>
                        </li>
                        @endcan
                        @if(session('system_mode') != 'restaurant')
                        @can('product_module.color.view')
                        <li class="@if(request()->segment(1) == 'color' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ColorController@index')}}">{{__('lang.color')}}</a>
                        </li>
                        @endcan
                        @can('product_module.grade.view')
                        <li class="@if(request()->segment(1) == 'grade' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('GradeController@index')}}">{{__('lang.grade')}}</a>
                        </li>
                        @endcan
                        @endif
                        @can('product_module.unit.view')
                        <li class="@if(request()->segment(1) == 'unit' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('UnitController@index')}}">{{__('lang.unit')}}</a>
                        </li>
                        @endcan
                        @can('product_module.size.view')
                        <li class="@if(request()->segment(1) == 'size' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SizeController@index')}}">{{__('lang.size')}}</a>
                        </li>
                        @endcan
                        @can('supplier_module.category.view')
                        <li class="@if(request()->segment(1) == 'supplier-category' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SupplierCategoryController@index')}}">{{__('lang.supplier_category')}}</a>
                        </li>
                        @endcan
                        @if(session('system_mode') == 'restaurant')
                        @can('settings.service_fee.view')
                        <li
                            class="@if(request()->segment(1) == 'service-fee' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ServiceFeeController@index')}}">{{__('lang.service_fee')}}</a>
                        </li>
                        @endcan
                        @can('settings.dining_room.view')
                        <li
                            class="@if(request()->segment(1) == 'dining-room' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('DiningRoomController@index')}}">{{__('lang.dining_room')}}</a>
                        </li>
                        @endcan
                        @can('settings.dining_table.view')
                        <li
                            class="@if(request()->segment(1) == 'dining-table' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('DiningTableController@index')}}">{{__('lang.dining_table')}}</a>
                        </li>
                        @endcan
                        @endif
                        @can('settings.delivery_zone.view')
                        <li
                            class="@if(request()->segment(1) == 'delivery-zone' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('DeliveryZoneController@index')}}">{{__('lang.delivery_zone')}}</a>
                        </li>
                        @endcan
                        @can('settings.exchange_rate.view')
                        <li
                            class="@if(request()->segment(1) == 'exchange-rate' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ExchangeRateController@index')}}">{{__('lang.exchange_rate')}}</a>
                        </li>
                        @endcan
                        @can('product_module.tax.view')
                        <li class="@if(request()->segment(1) == 'tax' && empty(request()->segment(2)) && request()->type == "product_tax") active @endif">
                            <a href="{{action('TaxController@index')}}?type=product_tax">{{__('lang.product_tax')}}</a>
                        </li>
                        @endcan
                        @can('product_module.tax.view')
                        <li class="@if(request()->segment(1) == 'tax' && empty(request()->segment(2)) && request()->type == "general_tax") active @endif">
                            <a href="{{action('TaxController@index')}}?type=general_tax">{{__('lang.general_tax')}}</a>
                        </li>
                        @endcan
                        @can('settings.store.view')
                        <li class="@if(request()->segment(1) == 'store' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('StoreController@index')}}">{{__('lang.stores')}}</a>
                        </li>
                        @endcan
                        @can('settings.store_pos.view')
                        <li
                            class="@if(request()->segment(1) == 'store-pos' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('StorePosController@index')}}">{{__('lang.pos_for_the_stores')}}</a>
                        </li>
                        @endcan
                        @can('settings.terms_and_conditions.view')
                        <li
                            class="@if(request()->segment(1) == 'terms-and-conditions' && request()->type == 'invoice')) active @endif">
                            <a
                                href="{{action('TermsAndConditionsController@index')}}?type=invoice">{{__('lang.list_invoice_terms_and_condition')}}</a>
                        </li>
                        @endcan

                        @can('settings.terms_and_conditions.view')
                        <li
                            class="@if(request()->segment(1) == 'terms-and-conditions' && request()->type == 'quotation')) active @endif">
                            <a
                                href="{{action('TermsAndConditionsController@index')}}?type=quotation">{{__('lang.list_quotation_terms_and_condition')}}</a>
                        </li>
                        @endcan
                        @can('settings.modules.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'settings' && request()->segment(2) == 'modules') active @endif">
                            <a href="{{action('SettingController@getModuleSettings')}}">{{__('lang.modules')}}</a>
                        </li>
                        @endcan
                        @can('settings.weighing_scale_setting.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'settings' && request()->segment(2) == 'get-weighing-scale-setting') active @endif">
                            <a
                                href="{{action('SettingController@getWeighingScaleSetting')}}">{{__('lang.weighing_scale_setting')}}</a>
                        </li>
                        @endcan
                        @can('settings.general_settings.view')
                        <li
                            class="@if(request()->segment(1) == 'settings' && request()->segment(2) == 'get-general-setting') active @endif">
                            <a
                                href="{{action('SettingController@getGeneralSetting')}}">{{__('lang.general_settings')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="@if(request()->segment(1) == 'tutorials' && empty(request()->segment(2))) active @endif">
                    <a href="{{action('TutorialController@getTutorialsCategoryGuide')}}"><i
                            class="fa fa-info-circle"></i><span>{{__('lang.tutorials')}}</span></a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

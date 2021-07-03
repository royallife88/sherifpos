<!-- Side Navbar -->
<nav class="side-navbar no-print @if(request()->segment(1) == 'pos') shrink @endif">
    <div class="side-navbar-wrapper">
        <!-- Sidebar Navigation Menus-->
        <div class="main-menu">
            <ul id="side-main-menu" class="side-menu list-unstyled">
                <li><a href="{{url('/home')}}"> <i
                            class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>

                @if(auth()->user()->can('product_module.product.create_and_edit') ||
                auth()->user()->can('product_module.product.view') ||
                auth()->user()->can('product_classification_tree.create_and_edit')||
                auth()->user()->can('product_module.barcode.create_and_edit'))
                <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-list"></i><span>{{__('lang.product')}}</span><span></a>
                    <ul id="product"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['product', 'product-classification-tree', 'barcode'])) show @endif">
                        @can('product_module.product.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'product' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ProductController@create')}}">{{__('lang.add_new_product')}}</a></li>
                        @endcan
                        @can('product_module.product.view')
                        <li
                            class="@if(request()->segment(1) == 'product' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ProductController@index')}}">{{__('lang.product_list')}}</a></li>
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
                            <a href="{{action('BarcodeController@create')}}">{{__('lang.print_barcode')}}</a></li>
                        @endcan
                    </ul>
                </li>
                @endif

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
                    </ul>
                </li>
                @endif

                @if(auth()->user()->can('stock.add_stock.view') ||
                auth()->user()->can('stock.add_stock.create_and_edit') )
                <li><a href="#stock" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-basket"></i><span>{{__('lang.stock')}}</span><span></a>
                    <ul id="stock"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['add-stock'])) show @endif">
                        @can('stock.add_stock.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'add-stock' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('AddStockController@create')}}">{{__('lang.add_new_stock')}}</a></li>
                        @endcan
                        @can('stock.stock.view')
                        <li
                            class="@if(request()->segment(1) == 'add-stock' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('AddStockController@index')}}">{{__('lang.view_all_added_stocks')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif

                @if(auth()->user()->can('sale.pos.create_and_edit') || auth()->user()->can('sale.pos.view') )
                <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-cart"></i><span>{{__('lang.sale')}}</span><span></a>
                    <ul id="sale"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['pos'])) show @endif">
                        @can('sale.pos.view')
                        <li
                            class="@if(request()->segment(1) == 'pos' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('SellPosController@index')}}">{{__('lang.sales_list')}}</a></li>
                        @endcan
                        @can('sale.pos.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'pos' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('SellPosController@create')}}">{{__('lang.pos')}}</a></li>
                        @endcan
                            {{-- @can('stock.stock.view')
                    <li class="@if(request()->segment(1) == 'add-stock' && empty(request()->segment(2))) active @endif"><a href="{{action('AddStockController@index')}}">{{__('lang.view_all_added_stocks')}}</a>
                    </li>
                    @endcan --}}
                </ul>
                </li>
                @endif
                @if(auth()->user()->can('expense.expenses.create_and_edit') || auth()->user()->can('expense.expenses.view')|| auth()->user()->can('expense.expense_categories.view')|| auth()->user()->can('expense.expense_categories.view')|| auth()->user()->can('expense.expense_beneficiaries.view')|| auth()->user()->can('expense.expense_beneficiaries.view') )
                <li><a href="#expense" aria-expanded="false" data-toggle="collapse"> <i
                            class="dripicons-minus"></i><span>{{__('lang.expense')}}</span><span></a>
                    <ul id="expense"
                        class="collapse list-unstyled @if(in_array(request()->segment(1), ['expense-cateogry', 'expense-beneficiary', 'expense'])) show @endif">
                        @can('expense.expense_categories.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense-cateogry' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ExpenseCategoryController@create')}}">{{__('lang.add_expense_category')}}</a></li>
                        @endcan
                        @can('expense.expense_categories.view')
                        <li
                            class="@if(request()->segment(1) == 'expense-cateogry' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ExpenseCategoryController@index')}}">{{__('lang.view_expense_categories')}}</a></li>
                        @endcan
                        @can('expense.expense_beneficiaries.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense-beneficiary' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ExpenseBeneficiaryController@create')}}">{{__('lang.add_expense_beneficiary')}}</a></li>
                        @endcan
                        @can('expense.expense_beneficiaries.view')
                        <li
                            class="@if(request()->segment(1) == 'expense-beneficiary' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ExpenseBeneficiaryController@index')}}">{{__('lang.view_expense_beneficiaries')}}</a></li>
                        @endcan
                        @can('expense.expense_beneficiaries.create_and_edit')
                        <li
                            class="@if(request()->segment(1) == 'expense' && request()->segment(2) == 'create') active @endif">
                            <a href="{{action('ExpenseController@create')}}">{{__('lang.add_new_expense')}}</a></li>
                        @endcan
                        @can('expense.expense_beneficiaries.view')
                        <li
                            class="@if(request()->segment(1) == 'expense' && empty(request()->segment(2))) active @endif">
                            <a href="{{action('ExpenseController@index')}}">{{__('lang.view_all_expenses')}}</a></li>
                        @endcan

                </ul>
                </li>
                @endif

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
                        <a href="{{action('CouponController@index')}}">{{__('lang.coupon')}}</a></li>
                    @endcan
                    @can('coupons_and_gift_cards.gift_card.view')
                    <li
                        class="@if(request()->segment(1) == 'gift-card' && request()->segment(2) == 'create') active @endif">
                        <a href="{{action('GiftCardController@index')}}">{{__('lang.gift_card')}}</a></li>
                    @endcan
                    {{-- @can('stock.stock.view')
                <li class="@if(request()->segment(1) == 'add-stock' && empty(request()->segment(2))) active @endif"><a href="{{action('AddStockController@index')}}">{{__('lang.view_all_added_stocks')}}</a>
            </li>
            @endcan --}}
            </ul>
            </li>
            @endif

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
                        <a href="{{action('CustomerController@create')}}">{{__('lang.add_new_customer')}}</a></li>
                    @endcan
                    @can('customer_module.customer.view')
                    <li class="@if(request()->segment(1) == 'customer' && empty(request()->segment(2))) active @endif">
                        <a href="{{action('CustomerController@index')}}">{{__('lang.view_all_customer')}}</a></li>
                    @endcan
                    @can('customer_module.customer_type.create_and_edit')
                    <li
                        class="@if(request()->segment(1) == 'customer-type' && request()->segment(2) == 'create') active @endif">
                        <a href="{{action('CustomerTypeController@create')}}">{{__('lang.add_new_customer_type')}}</a>
                    </li>
                    @endcan
                    @can('customer_module.customer_type.view')
                    <li
                        class="@if(request()->segment(1) == 'customer-type' && empty(request()->segment(2))) active @endif">
                        <a href="{{action('CustomerTypeController@index')}}">{{__('lang.view_all_customer_types')}}</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            @if(auth()->user()->can('supplier_module.supplier.create_and_edit') ||
            auth()->user()->can('supplier_module.supplier.view') )
            <li><a href="#supplier" aria-expanded="false" data-toggle="collapse"> <i
                        class="dripicons-user-group"></i><span>{{__('lang.suppliers')}}</span><span></a>
                <ul id="supplier"
                    class="collapse list-unstyled @if(in_array(request()->segment(1), ['supplier'])) show @endif">
                    @can('supplier_module.supplier.create_and_edit')
                    <li
                        class="@if(request()->segment(1) == 'supplier' && request()->segment(2) == 'create') active @endif">
                        <a href="{{action('SupplierController@create')}}">{{__('lang.add_new_supplier')}}</a></li>
                    @endcan
                    @can('supplier_module.supplier.view')
                    <li class="@if(request()->segment(1) == 'supplier' && empty(request()->segment(2))) active @endif">
                        <a href="{{action('SupplierController@index')}}">{{__('lang.view_all_supplier')}}</a></li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- START HR Management -->
            @if(auth()->user()->can('user_management.add_new_employee.view')
            || auth()->user()->can('user_management.employee.view')
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
                        <a href="{{action('JobController@index')}}">@lang('lang.jobs')</a></li>
                    @endcan
                    @can('hr_management.employee.create_and_edit')
                    <li
                        class="@if(request()->segment(2) == 'employee' && request()->segment(3) == 'create') active @endif">
                        <a href="{{action('EmployeeController@create')}}">@lang('lang.add_new_employee')</a></li>
                    @endcan
                    @can('hr_management.employee.view')
                    <li class="@if(request()->segment(2) == 'employee' && empty(request()->segment(3))) active @endif">
                        <a href="{{action('EmployeeController@index')}}">@lang('lang.employee_list')</a></li>
                    @endcan
                    @can('hr_management.leave_types.view')
                    <li
                        class="@if(request()->segment(2) == 'leave-type' && empty(request()->segment(3))) active @endif">
                        <a href="{{action('LeaveTypeController@index')}}">@lang('lang.leave_type')</a></li>
                    @endcan


                    @can('hr_management.leaves.view')
                    <li class="@if(request()->segment(2) == 'leave' && empty(request()->segment(3))) active @endif">
                        <a href="{{action('LeaveController@index')}}">@lang('lang.view_list_of_employees_in_leave')</a>
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
            <!-- END HR Management -->





            <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
                        class="dripicons-gear"></i><span>@lang('lang.settings')</span></a>
                <ul id="setting"
                    class="collapse list-unstyled @if(in_array(request()->segment(1), ['store', 'store-pos'])) show @endif">
                    @can('settings.store.view')
                    <li class="@if(request()->segment(1) == 'store' && empty(request()->segment(2))) active @endif"><a
                            href="{{action('StoreController@index')}}">{{__('lang.stores')}}</a></li>
                    @endcan
                    @can('settings.store_pos.view')
                    <li class="@if(request()->segment(1) == 'store-pos' && empty(request()->segment(2))) active @endif">
                        <a href="{{action('StorePosController@index')}}">{{__('lang.pos_for_the_stores')}}</a></li>
                    @endcan


                </ul>
            </li>
            </ul>
        </div>
    </div>
</nav>

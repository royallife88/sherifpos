<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\JobType;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\NumberOfLeave;
use App\Models\Product;
use App\Models\Store;
use App\Models\System;
use App\Models\User;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $notificationUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @param NotificationUtil $notificationUtil
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(Util $commonUtil, NotificationUtil $notificationUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->notificationUtil = $notificationUtil;
        $this->transactionUtil = $transactionUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Employee::leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('transactions', function ($join) {
                $join->on('transactions.employee_id', '=', 'employees.id')
                    ->where('transactions.type', '=', 'employee_commission');
            })
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id');

        if (!empty($request->start_date)) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        if (!empty(request()->start_time)) {
            $query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($request->employee_id)) {
            $query->where('transactions.employee_id', $request->employee_id);
        }
        if (!empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }
        if (!auth()->user()->can('superadmin') && auth()->user()->is_admin != 1) {
            $query->where('users.is_superadmin', 0);
        }

        $employees =  $query->select(
            'users.name',
            'users.email',
            'users.is_active',
            'employees.*',
            'job_types.job_title',
            DB::raw('SUM(transactions.final_total) as total_commission'),
            DB::raw('SUM(transaction_payments.amount) as total_commission_paid'),
        )
            ->groupBy('employees.id');

        if (request()->ajax()) {
            return DataTables::of($employees)
                ->addColumn('profile_photo', function ($row) {
                    if (!empty($row->getFirstMediaUrl('employee_photo'))) {
                        return '<img src="' . $row->getFirstMediaUrl('employee_photo') . '"
                        alt="photo" width="50" height="50">';
                    } else {
                        return '<img src="' . asset('/uploads/' . session('logo')) . '" alt="photo" width="50" height="50">';
                    }
                })
                ->addColumn('annual_leave_balance', function ($row) {
                    return $this->commonUtil->num_f(Employee::getBalanceLeave($row->id));
                })
                ->addColumn('age', function ($row) {
                    if (!empty($row->date_of_birth)) {
                        return Carbon::parse($row->date_of_birth)->diff(\Carbon\Carbon::now())->format('%y');
                    }
                })
                ->editColumn('date_of_start_working', function ($row) {
                    if (!empty($row->date_of_start_working)) {
                        return $this->commonUtil->format_date($row->date_of_start_working);
                    }
                })
                ->addColumn('current_status', function ($row) {
                    $html = '';
                    $today_on_leave = Leave::where('employee_id', $row->id)
                        ->whereDate('end_date', '>=', date('Y-m-d'))
                        ->whereDate('start_date', '<=', date('Y-m-d'))
                        ->where('status', 'approved')
                        ->first();

                    if (!empty($today_on_leave)) {
                        $html = '<label for="" style="font-weight: bold; color: red">' . __('lang.on_leave') . '</label>';
                    } else {
                        $status_today = Attendance::where('employee_id', $row->id)
                            ->whereDate('date', date('Y-m-d'))
                            ->first();

                        if (!empty($status_today)) {
                            if ($status_today->status == 'late' || $status_today->status == 'present') {
                                $html = '<label for="" style="font-weight: bold; color: green">' . __('lang.on_duty') . '</label>';
                            }
                            if ($status_today->status == 'on_leave') {
                                $html = '<label for="" style="font-weight: bold; color: red">' . __('lang.on_leave') . '</label>';
                            }
                        }
                    }
                    return $html;
                })
                ->addColumn('store', function ($row) {
                    return implode(', ', $row->store->pluck('name')->toArray());
                })
                ->addColumn('store_pos', function ($row) {
                    return $row->store_pos;
                })
                ->addColumn('commission', function ($row) {
                    $commission = $this->transactionUtil->calculateEmployeeCommissionPayments($row->id)['commission'];

                    return $this->commonUtil->num_f($commission);
                })
                ->addColumn('total_paid', function ($row) {
                    $total_paid = $this->transactionUtil->calculateEmployeeCommissionPayments($row->id)['total_paid'];

                    return $this->commonUtil->num_f($total_paid);
                })
                ->addColumn('due', function ($row) {
                    $due = $this->transactionUtil->calculateEmployeeCommissionPayments($row->id)['total_due'];

                    return $this->commonUtil->num_f($due);
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">' . __('lang.action') . '
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                        if (auth()->user()->can('hr_management.employee.view')) {
                            $html .= '<li>
                            <a href="' . action('EmployeeController@show', $row->id) . '"
                                class="btn"><i
                                    class="fa fa-eye"></i>
                                ' . __('lang.view') . '</a>
                        </li>';
                            $html .= '<li class="divider"></li>';
                        }
                        if (auth()->user()->can('hr_management.employee.create_and_edit')) {
                            $html .= '<li>
                                <a href="' . action('EmployeeController@edit', $row->id) . '"
                                    class="btn edit_employee"><i
                                        class="fa fa-pencil-square-o"></i>
                                    ' . __('lang.edit') . '</a>
                            </li>';
                            $html .= '<li class="divider"></li>';
                        }
                        if (auth()->user()->can('hr_management.employee.delete')) {
                            $html .= '<li>
                                <a data-href="' . action('EmployeeController@destroy', $row->id) . '"
                                    data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                    class="btn delete_item text-red"><i
                                        class="fa fa-trash"></i>
                                    ' . __('lang.delete') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('hr_management.suspend.create_and_edit')) {
                            $html .= '<li>
                                <a data-href="' . action('EmployeeController@toggleActive', $row->id) . '"
                                    class="btn toggle-active"><i
                                        class="fa fa-ban"></i>';
                            if ($row->is_active) {
                                $html .= __('lang.suspend');
                            } else {
                                $html .=   __('lang.reactivate');
                            }
                            $html .= '</a>
                                </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('hr_management.send_credentials.create_and_edit')) {
                            $html .= '<li>
                                <a href="' . action('EmployeeController@sendLoginDetails', $row->id) . '"
                                    class="btn"><i
                                        class="fa fa-paper-plane"></i>
                                    ' . __('lang.send_credentials') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('sms_module.sms.create_and_edit')) {
                            $html .= '<li>
                                <a href="' . action('SmsController@create', ['employee_id' => $row->id]) . '"
                                    class="btn"><i
                                        class="fa fa-comments-o"></i>
                                    ' . __('lang.send_sms') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('email_module.email.create_and_edit')) {
                            $html .= '<li>
                                <a href="' . action('EmailController@create', ['employee_id' => $row->id]) . '"
                                    class="btn"><i
                                        class="fa fa-envelope "></i>
                                    ' . __('lang.send_email') . '</a>
                            </li>';
                        }

                        $due = $this->transactionUtil->calculateEmployeeCommissionPayments($row->id)['total_due'];


                        $html .= '<li class="divider"></li>';
                        if ($due > 0) {
                            $html .= '<li>
                            <a href="' . action('WagesAndCompensationController@create', ['employee_id' => $row->id, 'payment_type' => 'commission']) . '"
                            class="btn"><i
                                class="fa fa-money "></i>
                            ' . __('lang.pay') . '</a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('hr_management.leaves.create_and_edit')) {
                            $html .= '<li>
                                <a class="btn btn-modal"
                                    data-href="' . action('LeaveController@create', ['employee_id' => $row->id]) . '"
                                    data-container=".view_modal">
                                    <i class="fa fa-sign-out"></i> ' . __('lang.leave') . '
                                </a>
                            </li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('hr_management.forfeit_leaves.create_and_edit')) {
                            $html .= '<li>
                                <a class="btn btn-modal"
                                    data-href="' . action('ForfeitLeaveController@create', ['employee_id' => $row->id]) . '"
                                    data-container=".view_modal">
                                    <i class="fa fa-ban"></i> ' . __('lang.forfeit_leave') . '
                                </a>
                            </li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
                )
                ->rawColumns([
                    'action',
                    'profile_photo',
                    'current_status',
                ])
                ->make(true);
        }


        return view('employee.index')->with(compact(
            'employees'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $week_days = Employee::getWeekDays();
        $payment_cycle = Employee::paymentCycle();
        $commission_type = Employee::commissionType();
        $commission_calculation_period = Employee::commissionCalculationPeriod();
        $modulePermissionArray = User::modulePermissionArray();
        $subModulePermissionArray = User::subModulePermissionArray();
        $jobs = JobType::getDropdown();
        $stores = Store::pluck('name', 'id')->toArray();
        $customer_types = CustomerType::getDropdown();
        $cashiers = Employee::getDropdownByJobType('Cashier');
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');
        $leave_types = LeaveType::get();

        return view('employee.create')->with(compact(
            'jobs',
            'stores',
            'week_days',
            'leave_types',
            'payment_cycle',
            'customer_types',
            'cashiers',
            'commission_type',
            'commission_calculation_period',
            'modulePermissionArray',
            'products',
            'subModulePermissionArray'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('hr_management.employee.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users|max:255',
            'name' => 'required|max:255',
            'password' => 'required|confirmed|max:255',
        ]);

        try {

            DB::beginTransaction();

            $data = $request->except('_token');
            $data['date_of_start_working'] = !empty($data['date_of_start_working']) ? Carbon::createFromFormat('m/d/Y', $data['date_of_start_working'])->format('Y-m-d') : null;
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::createFromFormat('m/d/Y', $data['date_of_birth'])->format('Y-m-d') : null;
            $data['fixed_wage'] = !empty($data['fixed_wage']) ? 1 : 0;
            $data['commission'] = !empty($data['commission']) ? 1 : 0;


            $user_data = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),

            ];
            $user = User::create($user_data);

            $employee = Employee::create([
                'employee_name' => $data['name'],
                'user_id' => $user->id,
                'store_id' => !empty($data['store_id']) ? $data['store_id'] : [],
                'pass_string' => Crypt::encrypt($data['password']),
                'date_of_start_working' => $data['date_of_start_working'],
                'date_of_birth' => $data['date_of_birth'],
                'job_type_id' => $data['job_type_id'],
                'mobile' => $data['mobile'],
                'annual_leave_per_year' => !empty($data['annual_leave_per_year']) ?  $data['annual_leave_per_year'] : 0,
                'sick_leave_per_year' => !empty($data['sick_leave_per_year']) ?  $data['sick_leave_per_year'] : 0,
                'number_of_days_any_leave_added' => !empty($data['number_of_days_any_leave_added']) ? $data['number_of_days_any_leave_added'] : 0,
                'fixed_wage' => $data['fixed_wage'],
                'fixed_wage_value' => $data['fixed_wage_value'] ?? 0,
                'payment_cycle' => $data['payment_cycle'],
                'commission' => $data['commission'],
                'commission_value' => $data['commission_value'] ?? 0,
                'commission_type' => $data['commission_type'],
                'commission_calculation_period' => $data['commission_calculation_period'],
                'commissioned_products' => !empty($data['commissioned_products']) ? $data['commissioned_products'] : [],
                'commission_customer_types' => !empty($data['commission_customer_types']) ? $data['commission_customer_types'] : [],
                'commission_stores' => !empty($data['commission_stores']) ? $data['commission_stores'] : [],
                'commission_cashiers' => !empty($data['commission_cashiers']) ? $data['commission_cashiers'] : [],
                'working_day_per_week' => !empty($data['working_day_per_week']) ? $data['working_day_per_week'] : [],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out']

            ]);


            if ($request->hasFile('photo')) {
                $employee->addMedia($request->photo)->toMediaCollection('employee_photo');
            }

            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file) {
                    $employee->addMedia($file)->toMediaCollection('employee_files');
                }
            }

            //add of update number of leaves
            $this->createOrUpdateNumberofLeaves($request, $employee->id);

            //assign permissions to employee
            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $key => $value) {
                    $permissions[] = $key;
                }

                if (!empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }

            // send email with the login details
            if ($request->submit == 'Send Credentials') {
                $this->notificationUtil->sendLoginDetails($employee->id);
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang.employee_added')
            ];

            return redirect()->to('/hrm/employee')->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }
    }

    public function createOrUpdateNumberofLeaves($request, $employee_id)
    {
        if (!empty($request->number_of_leaves)) {
            foreach ($request->number_of_leaves as $key => $value) {
                NumberOfLeave::updateOrCreate(
                    ['employee_id' => $employee_id, 'leave_type_id' => $key],
                    ['number_of_days' => $value['number_of_days'], 'created_by' => Auth::user()->id, 'enabled' => !empty($value['enabled']) ? 1 : 0]
                );
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $week_days = Employee::getWeekDays();
        $payment_cycle = Employee::paymentCycle();
        $commission_type = Employee::commissionType();
        $commission_calculation_period = Employee::commissionCalculationPeriod();
        $modulePermissionArray = User::modulePermissionArray();
        $subModulePermissionArray = User::subModulePermissionArray();

        $employee = Employee::leftjoin('users', 'employees.user_id', 'users.id')->where('employees.id', $id)->select('users.email', 'users.name', 'employees.*')->first();
        $user = User::find($employee->user_id);
        $jobs = JobType::getDropdown();
        $stores = Store::pluck('name', 'id')->toArray();
        $customer_types = CustomerType::getDropdown();
        $cashiers = Employee::getDropdownByJobType('Cashier');


        $number_of_leaves = LeaveType::leftjoin('number_of_leaves', function ($join) use ($id) {
            $join->on('leave_types.id', 'number_of_leaves.leave_type_id')->where('employee_id', $id);
        })
            ->select('leave_types.id', 'leave_types.name', 'leave_types.number_of_days_per_year as number_of_days', 'number_of_leaves.enabled')
            ->groupBy('leave_types.id')
            ->get();

        return view('employee.show')->with(compact(
            'jobs',
            'employee',
            'stores',
            'stores',
            'customer_types',
            'cashiers',
            'week_days',
            'payment_cycle',
            'commission_type',
            'commission_calculation_period',
            'number_of_leaves',
            'modulePermissionArray',
            'subModulePermissionArray',
            'user'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $week_days = Employee::getWeekDays();
        $payment_cycle = Employee::paymentCycle();
        $commission_type = Employee::commissionType();
        $commission_calculation_period = Employee::commissionCalculationPeriod();
        $modulePermissionArray = User::modulePermissionArray();
        $subModulePermissionArray = User::subModulePermissionArray();

        $employee = Employee::leftjoin('users', 'employees.user_id', 'users.id')->where('employees.id', $id)->select('users.email', 'users.name', 'employees.*')->first();
        $user = User::find($employee->user_id);
        $jobs = JobType::getDropdown();
        $stores = Store::pluck('name', 'id')->toArray();
        $customer_types = CustomerType::getDropdown();
        $cashiers = Employee::getDropdownByJobType('Cashier');
        $products = Product::orderBy('name', 'asc')->pluck('name', 'id');

        $number_of_leaves = LeaveType::leftjoin('number_of_leaves', function ($join) use ($id) {
            $join->on('leave_types.id', 'number_of_leaves.leave_type_id')->where('employee_id', $id);
        })
            ->select('leave_types.id', 'leave_types.name', 'leave_types.number_of_days_per_year as number_of_days', 'number_of_leaves.enabled')
            ->groupBy('leave_types.id')
            ->get();

        return view('employee.edit')->with(compact(
            'jobs',
            'employee',
            'stores',
            'stores',
            'customer_types',
            'cashiers',
            'week_days',
            'payment_cycle',
            'commission_type',
            'products',
            'commission_calculation_period',
            'number_of_leaves',
            'modulePermissionArray',
            'subModulePermissionArray',
            'user'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('hr_management.employee.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required|max:255'
        ]);

        try {

            DB::beginTransaction();

            $data = $request->except('_token');
            $data['date_of_start_working'] = !empty($data['date_of_start_working']) ? Carbon::createFromFormat('m/d/Y', $data['date_of_start_working'])->format('Y-m-d') : null;
            $data['date_of_birth'] = !empty($data['date_of_birth']) ? Carbon::createFromFormat('m/d/Y', $data['date_of_birth'])->format('Y-m-d') : null;
            $data['fixed_wage'] = !empty($data['fixed_wage']) ? 1 : 0;
            $data['commission'] = !empty($data['commission']) ? 1 : 0;

            $user_data = [
                'name' => $data['name'],
                'email' => $data['email']
            ];

            $employee_data = [
                'employee_name' => $data['name'],
                'store_id' => !empty($data['store_id']) ? $data['store_id'] : [],
                'date_of_start_working' => $data['date_of_start_working'],
                'date_of_birth' => $data['date_of_birth'],
                'job_type_id' => $data['job_type_id'],
                'mobile' => $data['mobile'],
                'annual_leave_per_year' => !empty($data['annual_leave_per_year']) ? $data['annual_leave_per_year'] : 0,
                'sick_leave_per_year' => !empty($data['sick_leave_per_year']) ?  $data['sick_leave_per_year'] : 0,
                'number_of_days_any_leave_added' => !empty($data['number_of_days_any_leave_added']) ?  $data['number_of_days_any_leave_added'] : 0,
                'fixed_wage' => $data['fixed_wage'],
                'fixed_wage_value' => $data['fixed_wage_value'] ?? 0,
                'payment_cycle' => $data['payment_cycle'],
                'commission' => $data['commission'],
                'commission_value' => $data['commission_value'] ?? 0,
                'commission_type' => $data['commission_type'],
                'commission_calculation_period' => $data['commission_calculation_period'],
                'commissioned_products' => !empty($data['commissioned_products']) ? $data['commissioned_products'] : [],
                'commission_customer_types' => !empty($data['commission_customer_types']) ? $data['commission_customer_types'] : [],
                'commission_stores' => !empty($data['commission_stores']) ? $data['commission_stores'] : [],
                'commission_cashiers' => !empty($data['commission_cashiers']) ? $data['commission_cashiers'] : [],
                'working_day_per_week' => !empty($data['working_day_per_week']) ? $data['working_day_per_week'] : [],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],

            ];
            if (!empty($request->input('password'))) {
                $validated = $request->validate([
                    'password' => 'required|confirmed|max:255',
                ]);
                $user_data['password'] = Hash::make($request->input('password'));
                $employee_data['pass_string'] = Crypt::encrypt($data['password']);;
            }

            $employee = Employee::find($id);
            $user = User::find($employee->user_id);
            User::where('id', $employee->user_id)->update($user_data);

            if ($request->hasFile('photo')) {
                $employee->clearMediaCollection('employee_photo');
                $employee->addMedia($request->photo)->toMediaCollection('employee_photo');
            }


            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file) {
                    $employee->addMedia($file)->toMediaCollection('employee_files');
                }
            }


            Employee::where('id', $id)->update($employee_data);

            //add of update number of leaves
            $this->createOrUpdateNumberofLeaves($request, $id);

            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $key => $value) {
                    $permissions[] = $key;
                }

                if (!empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang.employee_updated')
            ];

            return redirect()->to('/hrm/employee')->with('status', $output);
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
            return redirect()->back()->with('status', $output);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (!auth()->user()->can('hr_management.employee.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $employee = Employee::find($id);

                $user = User::find($employee->user_id);
                $user->delete();
                $employee->delete();

                $output = [
                    'success' => true,
                    'msg' => __("lang.deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => '__("messages.something_went_wrong")'
                ];
            }

            return $output;
        }
    }
    /**
     * check password
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkPassword($id)
    {
        $employee = Employee::where('user_id', $id)->first();

        if (auth()->user()->can('superadmin') || auth()->user()->is_admin == 1) {
            return ['success' => true];
        }
        if ((request()->value == $employee->pass_string)) {
            return ['success' => true];
        }
        return ['success' => false];
    }
    /**
     * get resource details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDetails($id)
    {
        $employee = Employee::leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
            ->where('employees.id', $id)
            ->select('users.name', 'employees.*', 'job_types.job_title')->first();

        $no_of_emplyee_same_job = Employee::where('job_type_id', $employee->job_type_id)->count();
        $leave_balance = Employee::getBalanceLeave($id);

        return ['employee' => $employee, 'no_of_emplyee_same_job' => $no_of_emplyee_same_job, 'leave_balance' => $leave_balance];
    }

    public function getBalanceLeaveDetails($id)
    {
        $leaves_details = NumberOfLeave::leftjoin('leave_types', 'number_of_leaves.leave_type_id', 'leave_types.id')
            ->where('number_of_leaves.employee_id', $id)
            ->where('enabled', 1)
            ->select('employee_id', 'leave_type_id', 'leave_types.name')
            ->get();

        return view('employee.partial.balance_leave_details')->with(compact(
            'leaves_details'
        ));
    }
    /**
     * get resource details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSameJobEmployeeDetails($id)
    {
        $employee = Employee::where('id', $id)->first();

        $employees = Employee::leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('jobs', 'employees.job_type_id', 'jobs.id')
            ->where('employees.job_type_id', $employee->job_type_id)
            ->select('users.name', 'employees.*', 'jobs.job_title')->get();



        return view('employee.partial.same_job_employee')->with(compact(
            'employees'
        ));
    }

    public function sendLoginDetails($employee_id)
    {
        try {
            $this->notificationUtil->sendLoginDetails($employee_id);

            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function toggleActive($id)
    {
        try {
            $employee = Employee::where('id', $id)->first();
            $user = User::find($employee->user_id);
            $user->is_active = !$user->is_active;

            $user->save();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * get dropdown html
     *
     * @return void
     */
    public function getDropdown()
    {
        $employees = Employee::orderBy('employee_name', 'asc')->pluck('employee_name', 'id');
        $employees_dp = $this->commonUtil->createDropdownHtml($employees, 'Please Select');

        return $employees_dp;
    }
}

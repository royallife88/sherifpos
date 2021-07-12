<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\JobType;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\WagesAndCompensation;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WagesAndCompensationController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = WagesAndCompensation::leftjoin('employees', 'wages_and_compensation.employee_id', 'employees.id')
            ->leftjoin('users', 'employees.user_id', 'users.id')
            ->leftjoin('job_types', 'employees.job_type_id', 'job_types.id')
            ->select(
                'wages_and_compensation.*',
                'employees.employee_name',
                'job_types.job_title',

            );

        if (!empty(request()->doc_start_date)) {
            $query->whereDate('wages_and_compensation.date_of_creation', '>=', request()->doc_start_date);
        }
        if (!empty(request()->doc_end_date)) {
            $query->whereDate('wages_and_compensation.date_of_creation', '<=', request()->doc_end_date);
        }
        if (!empty(request()->payment_end_date)) {
            $query->whereDate('wages_and_compensation.payment_date', '<=', request()->payment_end_date);
        }
        if (!empty(request()->payment_start_date)) {
            $query->whereDate('wages_and_compensation.payment_date', '>=', request()->payment_start_date);
        }
        if (!empty(request()->employee_id)) {
            $query->where('wages_and_compensation.employee_id', request()->employee_id);
        }
        if (!empty(request()->store_id)) {
            $query->where('employees.store_id', request()->store_id);
        }
        if (!empty(request()->job_type_id)) {
            $query->where('employees.job_type_id', request()->job_type_id);
        }
        if (!empty(request()->payment_type)) {
            $query->where('wages_and_compensation.payment_type', request()->payment_type);
        }
        if (!empty(request()->status)) {
            $query->where('wages_and_compensation.status', request()->status);
        }

        $wages_and_compensations =  $query->get();

        $payment_types = WagesAndCompensation::getPaymentTypes();
        $employees = Employee::getDropdown();
        $jobs = JobType::getDropdown();
        $stores = Store::getDropdown();

        return view('wages_and_compensations.index')->with(compact(
            'employees',
            'jobs',
            'stores',
            'payment_types',
            'wages_and_compensations',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::getDropdown();
        $payment_types = WagesAndCompensation::getPaymentTypes();

        return view('wages_and_compensations.create')->with(compact(
            'employees',
            'payment_types',
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
        try {
            $data = $request->except('_token', 'submit');

            $data['date_of_creation'] = Carbon::now();
            $data['created_by'] = Auth::user()->id;
            $data['other_payment'] = !empty($data['other_payment']) ? $data['other_payment'] : 0;
            $data['status'] = $request->submit == 'Paid' ? 'paid' : 'pending';
            $data['deductibles'] = !empty($data['deductibles']) ? $data['deductibles'] : 0;

            DB::beginTransaction();
            $wages_and_compensation = WagesAndCompensation::create($data);

            if ($request->hasFile('upload_files')) {
                $wages_and_compensation->addMedia($request->upload_files)->toMediaCollection('wages_and_compensation');
            }
            DB::commit();

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employees = Employee::getDropdown();
        $payment_types = WagesAndCompensation::getPaymentTypes();

        $wages_and_compensation = WagesAndCompensation::find($id);

        return view('wages_and_compensations.edit')->with(compact(
            'wages_and_compensation',
            'employees',
            'payment_types',
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
        try {
            $data = $request->except('_token', 'submit', '_method');

            $upload_files = null;
            if ($request->hasFile('upload_files')) {
                $file = $request->file('upload_files');
                $upload_files = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $upload_files);
                $data['upload_files'] = $upload_files;
            }

            $data['date_of_creation'] = Carbon::now();
            $data['created_by'] = Auth::user()->id;
            $data['other_payment'] = !empty($data['other_payment']) ? $data['other_payment'] : 0;
            $data['status'] = $request->submit == 'Paid' ? 'paid' : 'pending';

            $wages_and_compensation = WagesAndCompensation::where('id', $id)->first();
            $wages_and_compensation->update($data);


            if ($request->hasFile('upload_files')) {
                $wages_and_compensation->addMedia($request->upload_files)->toMediaCollection('wages_and_compensation');
            }

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

        return redirect()->to('/hr-management/wages-and-compensations')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $wages = WagesAndCompensation::find($id);
            $wages->delete();

            $output = [
                'success' => true,
                'msg' => __('lang.deleted')
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

    public function changeStatusToPaid($id)
    {

        try {
            $wages = WagesAndCompensation::find($id);
            $wages->status = 'paid';
            $wages->save();

            $output = [
                'success' => true,
                'msg' => __('lang.status_updated')
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
    /**
     * Calculate the salary and commission amount for employee
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function calculateSalaryAndCommission($employee_id, $payment_type)
    {
        $employee = Employee::find($employee_id);
        $amount = 0;

        if ($payment_type == 'salary') {
            if ($employee->fixed_wage == 1) {
                $fixed_wage_value = $employee->fixed_wage_value;
                $payment_cycle = $employee->payment_cycle;

                if ($payment_cycle == 'daily') {
                    $amount = $fixed_wage_value * 30;
                }
                if ($payment_cycle == 'weekly') {
                    $amount = $fixed_wage_value * 4;
                }
                if ($payment_cycle == 'bi-weekly') {
                    $amount = $fixed_wage_value * 2;
                }
                if ($payment_cycle == 'monthly') {
                    $amount = $fixed_wage_value * 1;
                }
            }
        }

        if ($payment_type == 'commission') {
            if ($employee->commission == 1) {

                $sold_value = 0;
                if ($employee->commission_type == 'sales') {

                    $sold_query = Transaction::leftjoin('customers', 'transactions.customer_id', 'customers.id')
                        ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
                        ->where('transactions.type', 'sell')
                        ->whereIn('customer_types.id', $employee->commission_customer_types)
                        ->where('status', 'final');

                    if (!empty(request()->acount_period_start_date) && !empty(request()->acount_period_end_date)) {
                        $sold_query->where('transactions.transaction_date', '>=', request()->acount_period_start_date);
                        $sold_query->where('transactions.transaction_date', '<=', request()->acount_period_end_date);
                    }

                    $sold_value = $sold_query->sum('final_total');
                }
                //TODO:: calculate the commission for profit
                $commission_value = $employee->commission_value;
                $amount = $this->commonUtil->calc_percentage($sold_value, $commission_value);
            }
        }



        return ['amount' => $amount];
    }
}

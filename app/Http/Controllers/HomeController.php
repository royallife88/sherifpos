<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Transaction;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $commonUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->middleware('auth');
        $this->commonUtil = $commonUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home.index');
    }

    public function myTransaction($year, $month)
    {
        $start = 1;
        $number_of_day = date('t', mktime(0, 0, 0, $month, 1, $year));
        while ($start <= $number_of_day) {
            if ($start < 10)
                $date = $year . '-' . $month . '-0' . $start;
            else
                $date = $year . '-' . $month . '-' . $start;
            $sale_generated[$start] = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->count();
            $sale_grand_total[$start] = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->sum('final_total');
            $purchase_generated[$start] = Transaction::where('type', 'add_stock')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->count();
            $purchase_grand_total[$start] = Transaction::where('type', 'add_stock')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->sum('final_total');
            $quotation_generated[$start] = Transaction::where('type', 'sell')->where('is_quotation', 1)->whereDate('transaction_date', Auth::id())->count();
            $quotation_grand_total[$start] = Transaction::where('type', 'sell')->where('is_quotation', 1)->whereDate('transaction_date', Auth::id())->sum('final_total');
            $start++;
        }
        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        return view('user.my_transactions', compact(
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'sale_generated',
            'sale_grand_total',
            'purchase_generated',
            'purchase_grand_total',
            'quotation_generated',
            'quotation_grand_total'
        ));
    }

    public function myHoliday($year, $month)
    {
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $employee = Employee::where('user_id', Auth::user()->id)->first();
        while ($start <= $number_of_day) {
            if ($start < 10) {
                $date = $year . '-' . $month . '-0' . $start;
            } else {
                $date = $year . '-' . $month . '-' . $start;
            }
            $holiday_found = Leave::where('employee_id', $employee->id)->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('status', 'approved')->first();
            if ($holiday_found) {
                $holidays[$start] = $this->commonUtil->format_date($holiday_found->start_date) . ' ' . __("lang.to") . ' ' . $this->commonUtil->format_date($holiday_found->end_date);
            } else {
                $holidays[$start] = false;
            }
            $start++;
        }

        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));

        return view('user.my_holidays', compact(
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'holidays'
        ));
    }
}

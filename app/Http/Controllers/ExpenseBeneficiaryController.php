<?php

namespace App\Http\Controllers;

use App\Models\ExpenseBeneficiary;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExpenseBeneficiaryController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expense_beneficiaries = ExpenseBeneficiary::leftjoin('users', 'expense_beneficiaries.created_by', 'users.id')->select('expense_beneficiaries.*', 'users.name as created_by')->get();

        $expense_categories = ExpenseCategory::orderBy('name', 'asc')->pluck('name', 'id');

        return view('expense_beneficiary.index')->with(compact(
            'expense_beneficiaries',
            'expense_categories'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expense_categories = ExpenseCategory::orderBy('name', 'asc')->pluck('name', 'id');

        return view('expense_beneficiary.create')->with(compact(
            'expense_categories'
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
            $data = $request->except('_token');

            $data['created_by'] = Auth::user()->id;

            ExpenseBeneficiary::create($data);

            $output = [
                'success' => true,
                'msg' => __('lang.expense_beneficiary_added')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
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
        $expense_beneficiary = ExpenseBeneficiary::find($id);
        $expense_categories = ExpenseCategory::orderBy('name', 'asc')->pluck('name', 'id');

        return view('expense_beneficiary.edit')->with(compact(
            'expense_beneficiary',
            'expense_categories'
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
            $data = $request->except('_token', '_method');
            ExpenseBeneficiary::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'msg' => __('lang.expense_beneficiary_updated')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
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

            ExpenseBeneficiary::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang.expense_beneficiary_deleted')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
}

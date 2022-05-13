<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\User;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftCardController extends Controller
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
        $query = GiftCard::where('id', '>', 0);

        if (!empty(request()->created_by)) {
            $query->where('created_by', request()->created_by);
        }
        if (!empty(request()->start_date)) {
            $query->whereDate('created_at', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('created_at', '<=', request()->end_date);
        }
        if (!empty(request()->status)) {
            $query->where('used', request()->status);
        }

        $gift_cards = $query->get();

        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        return view('gift_card.index')->with(compact(
            'gift_cards',
            'customers',
            'users',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;

        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $code = $this->generateCode();

        return view('gift_card.create')->with(compact(
            'quick_add',
            'customers',
            'code'
        ));
    }

    /**
     * gift_card a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            ['card_number' => ['required', 'max:255']],
            ['amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['balance'] = $this->commonUtil->num_uf($data['amount']);
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            $data['created_by'] = Auth::user()->id;
            DB::beginTransaction();

            $gift_card = GiftCard::create($data);

            $gift_card_id = $gift_card->id;

            DB::commit();
            $output = [
                'success' => true,
                'gift_card_id' => $gift_card_id,
                'card_number' => $gift_card->card_number,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->ajax()) {
            return $output;
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
        $gift_card = GiftCard::find($id);

        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');

        return view('gift_card.edit')->with(compact(
            'gift_card',
            'customers'
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

        $this->validate(
            $request,
            ['card_number' => ['required', 'max:255']],
            ['amount' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method');
            $data['amount'] = $this->commonUtil->num_uf($data['amount']);
            $data['balance'] = $this->commonUtil->num_uf($data['balance']);
            $data['active'] = 1;
            $data['expiry_date'] = !empty($data['expiry_date']) ? $this->commonUtil->uf_date($data['expiry_date']) : null;
            DB::beginTransaction();

            $gift_card = GiftCard::where('id', $id)->update($data);

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


        if ($request->quick_add) {
            return $output;
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
            GiftCard::find($id)->delete();
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

    public function getDropdown()
    {
        $gift_card = GiftCard::orderBy('name', 'asc')->pluck('name', 'id');
        $gift_card_dp = $this->commonUtil->createDropdownHtml($gift_card, 'Please Select');

        return $gift_card_dp;
    }

    public function generateCode()
    {
        $date = date('Y-m-d');
        $gift_card_count = GiftCard::whereDate('created_at', $date)->count() + 1;
        $count = str_pad($gift_card_count, 2, '0', STR_PAD_LEFT);
        $id = date('Y') . date('m') . date('d') . $count;
        return $id;
    }

    public function toggleActive($id)
    {
        try {
            $gift_card = GiftCard::where('id', $id)->first();
            $gift_card->active = !$gift_card->active;

            $gift_card->save();
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

    public function getDetails($gift_card_number)
    {
        $gift_card_details = GiftCard::where('card_number', $gift_card_number)->where('balance', '>', 0)->first();

        if (empty($gift_card_details)) {
            return [
                'success' => false,
                'msg' => __('lang.invalid_card_number')
            ];
        }
        if ($gift_card_details->active == 0) {
            return [
                'success' => false,
                'msg' => __('lang.gift_card_suspended')
            ];
        }
        if (!empty($gift_card_details->expiry_date)) {
            if (Carbon::now()->gt(Carbon::parse($gift_card_details->expiry_date))) {
                return [
                    'success' => false,
                    'msg' => __('lang.gift_card_expired')
                ];
            }
        }

        return [
            'success' => true,
            'data' => $gift_card_details->toArray()
        ];
    }
}

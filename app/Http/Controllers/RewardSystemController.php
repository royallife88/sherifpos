<?php

namespace App\Http\Controllers;

use App\Models\RewardSystem;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RewardSystemController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function getDetailsByCustomer($customer_id)
    {
        if (request()->ajax()) {
            $query = RewardSystem::leftjoin('gift_cards', 'reward_systems.gift_card_id', 'gift_cards.id')
                ->where('referred_by', $customer_id);
            $rewards =    $query->select(
                'reward_systems.*',
                'gift_cards.amount as gift_card_amount',
            );

            return DataTables::of($rewards)
                // ->setTotalRecords()
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->editColumn('type', function ($row) {
                    $type_array = ['money' => __('lang.money'), 'loyalty_point' => __('lang.loyalty_point'), 'gift_card' => __('lang.gift_card'), 'discount' => __('lang.discount')];
                    return $type_array[$row->type];
                })
                ->addColumn('value', function ($row) {
                    if ($row->type == 'money') {
                        return $this->commonUtil->num_f($row->amount);
                    }
                    if ($row->type == 'gift_card') {
                        return $this->commonUtil->num_f($row->gift_card_amount);
                    }
                    if ($row->type == 'loyalty_point') {
                        return $this->commonUtil->num_f($row->loyalty_points);
                    }
                    if ($row->type == 'discount') {
                        return $this->commonUtil->num_f($row->discount);
                    }
                })->rawColumns([
                    'created_by',
                ])
                ->make(true);
        }
    }
}

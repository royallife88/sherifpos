<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\TransactionSellLine;
use App\Models\Variation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePurchasePriceForTransactionSellLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:updatePurchasePriceForTransactionSellLines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update purchase price for transaction sell lines for fix only';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $transactions_sell_lines = TransactionSellLine::get();

            foreach ($transactions_sell_lines as $sell_line) {
                $variation = Variation::where('product_id', $sell_line->product_id)->where('id', $sell_line->variation_id)->first();
                if (!empty($variation)) {
                    $sell_line->purchase_price = $variation->default_purchase_price;
                    $sell_line->save();
                } else {
                    $product = Product::find($sell_line->product_id);
                    if (!empty($product)) {
                        $sell_line->purchase_price = $product->purchase_price;
                        $sell_line->save();
                    }
                }
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

        print_r($output);
    }
}

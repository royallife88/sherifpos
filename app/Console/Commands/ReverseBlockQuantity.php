<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Utils\ProductUtil;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReverseBlockQuantity extends Command
{
     /**
     * All Utils instance.
     *
     */
    protected $productUtil;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:reverseBlockQuantity';

    /**
     * Reverse the block quantity if days are over.
     *
     * @var string
     */
    protected $description = 'Reverse the block quantity if days are over';

     /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        parent::__construct();

        $this->productUtil = $productUtil;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transaction::where('is_quotation', 1)->where('block_qty', 1)->select('transactions.*')->get();

        foreach($transactions as $transaction){
            $reverse_block_date = Carbon::parse($transaction->transaction_date)->addDays($transaction->block_for_days)->format('Y-m-d');

            if(Carbon::now()->format('Y-m-d') == $reverse_block_date){
                foreach($transaction->transaction_sell_lines as $sell_line){
                    $this->productUtil->updateBlockQuantity($sell_line->product_id, $sell_line->variation_id, $transaction->store_id, $sell_line->quantity, 'subtract');
                }
            }
        }

    }
}

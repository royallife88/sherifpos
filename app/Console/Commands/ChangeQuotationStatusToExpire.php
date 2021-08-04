<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeQuotationStatusToExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:changeQuotationStatusToExpire';

    /**
     * Change quotation status to expire after validity days.
     *
     * @var string
     */
    protected $description = 'Change quotation status to expire after validity days';

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
        $transactions = Transaction::where('is_quotation', 1)->where('status', 'draft')->select('transactions.*')->where('id', 380)->get();

        foreach ($transactions as $transaction) {
            $expiry_date = Carbon::parse($transaction->transaction_date)->addDays($transaction->validity_days)->format('Y-m-d');

            if (Carbon::now()->format('Y-m-d') == $expiry_date) {
                $transaction->status = 'expired';
                $transaction->save();
            }
        }
    }
}

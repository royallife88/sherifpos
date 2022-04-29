@php
$currencies_obj = App\Models\ExchangeRate::leftjoin('currencies', 'exchange_rates.received_currency_id', 'currencies.id')
    ->where(function ($q) {
        $q->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', date('Y-m-d'));
    })
    ->select('received_currency_id as currency_id', 'currencies.symbol', 'conversion_rate')
    ->get()
    ->toArray();

$default_currency_id = App\Models\System::getProperty('currency');
$default_currency = App\Models\Currency::where('id', $default_currency_id)
    ->select('id as currency_id', 'symbol')
    ->first()
    ->toArray();
$d['currency_id'] = $default_currency['currency_id'];
$d['symbol'] = $default_currency['symbol'];
$d['conversion_rate'] = 1;
$currencies_obj[] = $d;

@endphp
<script>
    var currency_obj = <?php echo json_encode($currencies_obj); ?>;
</script>

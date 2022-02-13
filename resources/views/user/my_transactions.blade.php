@extends('layouts.app')
@section('title', __('lang.my_transactions'))
@section('content')
<section>
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.my_transactions')</h4>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered"
                            style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                            <thead>
                                <tr>
                                    <th><a href="{{url('my-transactions/'.$prev_year.'/'.$prev_month)}}"><i
                                                class="fa fa-arrow-left"></i> @lang('lang.previous')</a></th>
                                    <th colspan="5" class="text-center">
                                        {{date("F", strtotime($year.'-'.$month.'-01')).' ' .$year}}</th>
                                    <th><a href="{{url('my-transactions/'.$next_year.'/'.$next_month)}}">@lang('lang.next')
                                            <i class="fa fa-arrow-right"></i></a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>@lang('lang.sunday')</strong></td>
                                    <td><strong>@lang('lang.monday')</strong></td>
                                    <td><strong>@lang('lang.tuesday')</strong></td>
                                    <td><strong>@lang('lang.wednesday')</strong></td>
                                    <td><strong>@lang('lang.thursday')</strong></td>
                                    <td><strong>@lang('lang.friday')</strong></td>
                                    <td><strong>@lang('lang.saturday')</strong></td>
                                </tr>
                                <?php
			    	$i = 1;
			    	$flag = 0;
			    	while ($i <= $number_of_day) {
			    		echo '<tr>';
			    		for($j=1 ; $j<=7 ; $j++){
			    			if($i > $number_of_day)
			    				break;

			    			if($flag){
			    				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
			    					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
			    				else
			    					echo '<td><p><strong>'.$i.'</strong></p>';

			    				if($sale_generated[$i]) {
			    					echo '<strong>'.__("lang.sales").':</strong> '.$sale_generated[$i].'<br>';
			    				}
			    				if($sale_grand_total[$i]) {
			    					echo '<strong>'.__("lang.grand_total").':</strong> '.$sale_grand_total[$i].'<br><br>';
			    				}
			    				if($purchase_generated[$i]) {
			    					echo '<strong>'.__("lang.purchases").':</strong> '.$purchase_generated[$i].'<br>';
			    				}
			    				if($purchase_grand_total[$i]) {
			    					echo '<strong>'.__("lang.grand_total").':</strong> '.$purchase_grand_total[$i].'<br><br>';
			    				}
			    				if($quotation_generated[$i]) {
			    					echo '<strong>'.__("lang.quotations").':</strong> '.$quotation_generated[$i].'<br>';
			    				}
			    				if($quotation_grand_total[$i]) {
			    					echo '<strong>'.__("lang.grand_total").':</strong> '.$quotation_grand_total[$i].'<br><br>';
			    				}
			    				echo '</td>';
			    				$i++;
			    			}
			    			elseif($j == $start_day){
			    				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
			    					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
			    				else
			    					echo '<td><p><strong>'.$i.'</strong></p>';

			    				if($sale_generated[$i]) {
			    					echo '<strong>'.trans("lang.sales").':</strong> '.$sale_generated[$i].'<br>';
			    				}
			    				if($sale_grand_total[$i]) {
			    					echo '<strong>'.trans("lang.grand_total").':</strong> '.$sale_grand_total[$i].'<br><br>';
			    				}
			    				if($purchase_generated[$i]) {
			    					echo '<strong>'.trans("lang.purchases").':</strong> '.$purchase_generated[$i].'<br>';
			    				}
			    				if($purchase_grand_total[$i]) {
			    					echo '<strong>'.trans("lang.grand_total").':</strong> '.$purchase_grand_total[$i].'<br><br>';
			    				}
			    				if($quotation_generated[$i]) {
			    					echo '<strong>'.trans("lang.quotations").':</strong> '.$quotation_generated[$i].'<br>';
			    				}
			    				if($quotation_grand_total[$i]) {
			    					echo '<strong>'.trans("lang.grand_total").':</strong> '.$quotation_grand_total[$i].'<br><br>';
			    				}
			    				echo '</td>';
			    				$flag = 1;
			    				$i++;
			    				continue;
			    			}
			    			else {
			    				echo '<td></td>';
			    			}
			    		}
			    	    echo '</tr>';
			    	}
			    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

</script>
@endsection

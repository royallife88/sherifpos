@php
$moment_time_format = App\Models\System::getProperty('time_format') == '12' ? 'hh:mm A' : 'HH:mm';
@endphp
<script>
    var moment_time_format = "{{$moment_time_format}}";
</script>
<script type="text/javascript" src="{{asset('js/lang/'.session('language').'.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery/jquery.timepicker.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/popper.js/umd/popper.min.js') }}">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script type="text/javascript" src="{{asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/daterange/js/moment.min.js') }}"></script>

<script type="text/javascript" src="{{asset('vendor/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/bootstrap-datepicker/locales/bootstrap-datepicker.'.session('language').'.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>

<script type="text/javascript" src="{{asset('vendor/bootstrap-toggle/js/bootstrap-toggle.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/bootstrap/js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/keyboard/js/jquery.keyboard.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/keyboard/js/jquery.keyboard.extension-autocomplete.js') }}">
</script>
<script type="text/javascript" src="{{asset('js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery.cookie/jquery.cookie.js') }}">
</script>
<script type="text/javascript" src="{{asset('vendor/chart.js/Chart.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script type="text/javascript"
    src="{{asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script type="text/javascript" src="{{asset('js/charts-custom.js') }}"></script>
<script type="text/javascript" src="{{asset('js/front.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/daterange/js/knockout-3.4.2.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/daterange/js/daterangepicker.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript" src="{{asset('js/dropzone.js') }}"></script>
<script type="text/javascript" src="{{asset('js/bootstrap-treeview.js') }}"></script>

<!-- table sorter js-->
<script type="text/javascript" src="{{asset('vendor/datatable/pdfmake.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/vfs_fonts.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/buttons.bootstrap4.min.js') }}">
    ">
</script>
<script type="text/javascript" src="{{asset('vendor/datatable/buttons.colVis.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/buttons.html5.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/buttons.print.min.js') }}"></script>

<script type="text/javascript" src="{{asset('vendor/datatable/sum().js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/dataTables.checkboxes.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/datatable/date-eu.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/accounting.min.js') }}"></script>
<script type="text/javascript" src="{{asset('vendor/toastr/toastr.min.js')}}"></script>

<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js">
</script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js">
</script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js">
</script>
<script type="text/javascript" src="{{asset('vendor/cropperjs/cropper.min.js') }}"></script>
<script type="text/javascript" src="{{asset('js/printThis.js') }}"></script>
<script type="text/javascript" src="{{asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{asset('js/currency_exchange.js') }}"></script>
<script type="text/javascript" src="{{asset('js/customer.js') }}"></script>
<script type="text/javascript" src="{{asset('js/cropper.js') }}"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

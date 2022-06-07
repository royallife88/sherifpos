@extends('layouts.app')
@section('title', __('lang.supplier'))
@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.add_supplier')</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                            {!! Form::open(['url' => action('SupplierController@store'), 'id' => 'supplier-form', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                            @include('supplier.partial.create_supplier_form')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="button" value="{{ trans('lang.submit') }}" id="submit-btn"
                                            class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $('#supplier-type-form').submit(function() {
            $(this).validate();
            if ($(this).valid()) {
                $(this).submit();
            }
        });

        $(document).on("click", "#submit-btn", function(e) {
            e.preventDefault();
            if ($('#supplier-form').valid()) {
                $('#supplier-form').submit();
            }
        });
        $(document).on("submit", "form#quick_add_supplier_category_form", function(e) {
            $("form#quick_add_supplier_category_form").validate();
            e.preventDefault();
            var data = new FormData(this);
            $.ajax({
                method: "post",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        swal("Success", result.msg, "success");
                        $(".view_modal").modal("hide");
                        var supplier_category_id = result.supplier_category_id;
                        $.ajax({
                            method: "get",
                            url: "/supplier-category/get-dropdown",
                            data: {},
                            contactType: "html",
                            success: function(result) {
                                $("select#supplier_category_id").html(result);
                                $("select#supplier_category_id").val(supplier_category_id);
                                $("#supplier_category_id").selectpicker("refresh");

                            },
                        });
                    } else {
                        swal("Error", result.msg, "error");
                    }
                },
            });
        });
    </script>
@endsection

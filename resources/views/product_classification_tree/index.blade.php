@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')
<style>
    .panel-heading {
        position: relative;
    }

    .panel-heading h4 {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .panel-heading[data-toggle="collapse"]:after {
        font-family: 'FontAwesome';
        content: "\f054";
        /* "play" icon */
        position: absolute;
        color: #b0c5d8;
        font-size: 18px;
        line-height: 22px;
        right: 20px;
        top: calc(50% - 10px);

        /* rotate "play" icon from > (right arrow) to down arrow */
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transform: rotate(-90deg);
    }

    .panel-heading[data-toggle="collapse"].collapsed:after {
        /* rotate "play" icon from > (right arrow) to ^ (up arrow) */
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg);
    }
</style>
<div class="container-fluid">


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.product_classification_tree')</h4>
                </div>
                <div class="card-body">
                    <div id="tree"></div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

@section('javascript')
<script>
    function getTree() {
  return [
   {!! $json_tree !!}
  ];
}

$('#tree').treeview({
    data: getTree(),
    levels: 25,
    // enableLinks: true,
});

$('#tree').on('nodeSelected', function(event, data) {
    if(data.nodes.length !== 0){
        $('#tree').treeview('toggleNodeExpanded', [ data.nodeId, { silent: true } ]);
    }
    console.log(data.nodes.length);
});
$('#tree').on('nodeUnselected', function(event, data) {
    if(data.nodes.length !== 0){
    $('#tree').treeview('toggleNodeExpanded', [ data.nodeId, { silent: true } ]);
}
    console.log(data.nodes.length);
});
</script>
@endsection

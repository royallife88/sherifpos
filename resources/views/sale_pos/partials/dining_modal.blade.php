<!-- coupon modal -->

<style>
    a:hover,
    a:focus {
        text-decoration: none;
        outline: none;
    }

    .vertical-tab {
        font-family: 'Montserrat', sans-serif;
        display: table;
    }

    .vertical-tab .nav-tabs {
        width: 16%;
        min-width: 16%;
        border: none;
        vertical-align: top;
        display: table-cell;
    }

    .vertical-tab .nav-tabs li {
        float: none;
    }

    .vertical-tab .nav-tabs li a {
        color: rgb(51, 50, 50);
        background: #fff;
        font-size: 19px;
        font-weight: 500;
        text-transform: capitalize;
        text-align: center;
        padding: 18px 25px 16px;
        margin: 0 0 10px 0;
        border: none;
        border-radius: 0;
        box-shadow: 0 0 7px rgba(0, 0, 0, 0.1) inset;
        overflow: hidden;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease 0s;
        border: 1px solid rgb(248, 200, 111);
        border-radius: 5px;
        width: 132px;
        display: inline-block;
        text-size-adjust: auto;
    }

    .vertical-tab .nav-tabs li a:hover,
    .vertical-tab .nav-tabs li.active a,
    .vertical-tab .nav-tabs li.active a:hover {
        color: rgb(51, 50, 50);
        border: 1px solid rgb(248, 200, 111);
        border-radius: 5px;
    }

    .vertical-tab .nav-tabs li a.active {
        background: #06bc61;
        border: 1px solid rgb(248, 200, 111);
        border-radius: 5px;
        color: #fff;
    }

    .vertical-tab .nav-tabs li a:before,
    .vertical-tab .nav-tabs li a:after {
        content: "";
        background-color: #06bc61;
        width: 50.5%;
        height: 100%;
        opacity: 0;
        transform: perspective(300px) rotateX(-100deg);
        position: absolute;
        top: 0;
        left: 0;
        z-index: -1;
        transition: all 0.4s ease 0s;
    }

    .vertical-tab .tab-content {
        color: #555;
        background-color: #fff;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 1px;
        line-height: 25px;
        padding: 15px 20px 10px;
        box-shadow: 0 0 7px rgba(0, 0, 0, 0.1) inset;
        display: table-cell;
    }

    .vertical-tab .tab-content h3 {
        color: #06bc61;
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        margin: 0 0 7px;
    }

    @media only screen and (max-width: 479px) {
        .vertical-tab .nav-tabs {
            width: 100%;
            margin: 0 0 15px;
            display: block;
        }

        .vertical-tab .nav-tabs li a {
            padding: 15px 10px 14px;
            margin-right: 0;
        }

        .vertical-tab .tab-content {
            font-size: 14px;
            display: block;
        }
    }

</style>
<div role="document" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body" id="dining_content">
            @include('sale_pos.partials.dining_content')

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang(
                'lang.close')</button>
        </div>
    </div>
</div>
<script>
    $(".nav-tabs li a").click(function() {
        $(".nav-tabs li").removeClass("active");
        $(this).parent().addClass("active");
    });
</script>

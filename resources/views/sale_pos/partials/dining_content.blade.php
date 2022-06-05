<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="vertical-tab" role="tabpane">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    @foreach ($dining_rooms as $dining_room)
                        <li role="presentation"
                            class="@if ($loop->index == 0 && empty($active_tab_id)) active @elseif($dining_room->id == $active_tab_id) active @endif">
                            <a class="@if ($loop->index == 0 && empty($active_tab_id)) active show @elseif($dining_room->id == $active_tab_id) active @endif"
                                href="#dining_tab{{ $dining_room->id }}" aria-controls="home" role="tab"
                                data-toggle="tab"> {{ $dining_room->name }}</a>
                        </li>
                    @endforeach

                    <li role="presentation"><a data-href="{{ action('DiningRoomController@create') }}"
                            style="background-color: orange; color: #fff; font-size:12px; padding: 18px 10px 16px; display:block "
                            data-container=".view_modal" class="btn btn-modal add_dining_room" aria-controls="messages"
                            role="tab" ">@lang('lang.add_dining_room')</a></li>
                    </ul>
                <!-- Tab panes -->
                <div class=" tab-content tabs">
                            @foreach ($dining_rooms as $dining_room)
                                <div role="tabpane"
                                    class="tab-pane fade @if ($loop->index == 0 && empty($active_tab_id)) in active show @elseif($dining_room->id == $active_tab_id) in active show @endif"
                                    id="dining_tab{{ $dining_room->id }}">
                                    <div class="row" style="line-height: 13px;">
                                        @foreach ($dining_room->dining_tables as $dining_table)
                                            @if ($dining_table->status == 'available')
                                                <div class="col-md-2 text-center table_action"
                                                    data-table_id="{{ $dining_table->id }}">
                                                    <p style="padding: 0px; margin: 0px; color:red;">
                                                        {{ $dining_table->name }} </p>
                                                    <img src="{{ asset('images/green-table.jpg') }}" alt="table"
                                                        style="height: 70px; width: 80px;">
                                                </div>
                                            @endif
                                            @if ($dining_table->status == 'reserve')
                                                <div class="col-md-2 text-center table_action"
                                                    data-table_id="{{ $dining_table->id }}">
                                                    <p style="padding: 0px; margin: 0px; color:red;">
                                                        {{ $dining_table->name }} </p>
                                                    <img src="{{ asset('images/black-table.jpg') }}" alt="table"
                                                        style="height: 70px; width: 80px;">
                                                    @if (!empty($dining_table->customer_name))
                                                        <p style="padding: 0px; margin: 0px; color:black;">
                                                            {{ $dining_table->customer_name }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($dining_table->customer_mobile_number))
                                                        <p style="padding: 0px; margin: 0px; color:black;">
                                                            {{ $dining_table->customer_mobile_number }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($dining_table->date_and_time))
                                                        <p style="padding: 0px; margin: 0px; color:black;">
                                                            {{ @format_datetime($dining_table->date_and_time) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                            @if ($dining_table->status == 'order')
                                                <div class="col-md-2 text-center order_table"
                                                    data-table_id="{{ $dining_table->id }}">
                                                    <a href="{{ action('SellPosController@edit', $dining_table->current_transaction_id) }}"
                                                        target="_blank" rel="noopener noreferrer">
                                                        <p style="padding: 0px; margin: 0px; color:red;">
                                                            {{ $dining_table->name }} </p>
                                                        <img src="{{ asset('images/red-table.jpg') }}" alt="table"
                                                            style="height: 70px; width: 80px;">
                                                        <p style="padding: 0px; margin: 0px; color:red;">
                                                            @if (!empty($dining_table->transaction))
                                                                {{ @num_format($dining_table->transaction->final_total) }}
                                                            @endif
                                                        </p>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach

                                        <div class="col-md-2">
                                            <button class="btn btn-modal add_dining_table"
                                                style="background-color: orange; padding: 18px 10px 16px; color: #fff; margin-top: 15px;"
                                                data-href="{{ action('DiningTableController@create', ['room_id' => $dining_room->id]) }}"
                                                data-container=".view_modal">@lang('lang.add_new_table')</button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
            </div>
        </div>
    </div>
</div>
</div>

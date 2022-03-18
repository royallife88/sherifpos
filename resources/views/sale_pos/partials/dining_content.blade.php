<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="vertical-tab" role="tabpane">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    @foreach ($dining_rooms as $dining_room)
                        <li role="presentation" class="@if ($loop->index == 0) active @endif"><a
                                href="#dining_tab{{ $dining_room->id }}" aria-controls="home" role="tab"
                                data-toggle="tab"> {{ $dining_room->name }}</a></li>
                    @endforeach

                    <li role="presentation"><a data-href="{{ action('DiningRoomController@create') }}"
                            style="background-color: orange; color: #fff; font-size:12px; padding: 18px 10px 16px; display:block "
                            data-container=".view_modal" class="btn btn-modal add_dining_room" aria-controls="messages"
                            role="tab" ">@lang('lang.add_dining_room')</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="     tab-content tabs">
                            @foreach ($dining_rooms as $dining_room)
                                <div role="tabpane"
                                    class="tab-pane fade @if ($loop->index == 0) in active show @endif"
                                    id="dining_tab{{ $dining_room->id }}">
                                    <div class="row">
                                        @foreach ($dining_room->dining_tables as $dining_table)
                                            <div class="col-md-2">
                                                @if($dining_table->status == 'available')
                                                <img src="{{ asset('images/green-table.jpg') }}" alt="table" style="height: 70px; width: 80px;">
                                                @endif
                                            </div>
                                        @endforeach

                                        <div class="col-md-2">
                                            <button class="btn btn-modal add_dining_table" style="background-color: orange; padding: 18px 10px 16px; color: #fff;" data-href="{{action('DiningTableController@create', ['room_id' => $dining_room->id])}}" data-container=".view_modal">@lang('lang.add_new_table')</button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
            </div>
        </div>
    </div>
</div>
</div>

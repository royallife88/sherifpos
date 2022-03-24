@php
$config_langs = config('constants.langs');
@endphp


<table class="table hide" id="translation_table">
    <tbody>
        @foreach ($config_langs as $key => $lang)
            <tr>
                <td> <input class="form-control" type="text" name="translations[{{ $attribute }}][{{ $key }}]"
                        value="@if (!empty($translations[$attribute][$key])) {{ $translations[$attribute][$key] }} @endif"
                        placeholder="{{ $lang['full_name'] }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>

<table style="border: 1px solid #d1cece; widht: 100%;">
    @if(!empty($data['site_title']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.site_title'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['site_title']}}</td>
    </tr>
    @endif
    @if(!empty($data['user_name']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.name'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['user_name']}}</td>
    </tr>
    @endif
    @if(!empty($data['country_code']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.country_code'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['country_code']}}</td>
    </tr>
    @endif
    @if(!empty($data['country_code']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.phone_number'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['phone_number']}}</td>
    </tr>
    @endif
    @if(!empty($data['country_code']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.email'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['email']}}</td>
    </tr>
    @endif
    @if(!empty($data['message']))
    <tr>
        <td style="border: 1px solid #d1cece;">
            @lang('lang.message'):
        </td>
        <td style="border: 1px solid #d1cece;">{{$data['message']}}</td>
    </tr>
    @endif
</table>

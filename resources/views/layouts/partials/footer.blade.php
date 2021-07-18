<footer class="main-footer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <p>&copy; {{App\Models\System::getProperty('site_title')}} @if(!empty(App\Models\System::getProperty('developed_by')))| @lang('lang.developed_by') <span class="external">{!!App\Models\System::getProperty('developed_by')!!}</span>@endif</p>
        </div>
      </div>
    </div>
  </footer>

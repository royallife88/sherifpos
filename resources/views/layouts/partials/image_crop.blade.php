<div class="image_area">
    <label for="upload_image">
        <img src="@if(!empty($image_url)){{$image_url}}@endif" id="uploaded_image" class="" />
        <input type="hidden" name="uploaded_image_name" id="uploaded_image_name" value="">
        <div class="mt-3">
            <div class="text"><i class="fa fa-upload"></i> @lang('lang.select_image')</div>
        </div>
        <input type="file" name="image" class="image" id="upload_image" style="display:none" />
    </label>
</div>

<div class="image_area">
    <label for="upload_image">
        <img src="@if (!empty($image_url)) {{ $image_url }} @endif" id="uploaded_image"
            class="" />
        <input type="hidden" name="uploaded_image_name" id="uploaded_image_name" value="">
        <div class="mt-3" class="btn btn-primary" style="background-color: #7c5cc4; border-color: #7c5cc4; color: #fff; padding: 8px 10px; border-radius: 3px; width: 115px;">
            @lang('lang.select_image')
        </div>
        <input type="file" name="image" class="image" id="upload_image" style="display:none" />
    </label>
</div>

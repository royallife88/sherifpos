<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\WagesAndCompensation;

class GeneralController extends Controller
{
    public function viewUploadedFiles($model_name, $model_id)
    {
        $collection_name = request()->collection_name;

        $path = 'App\Models';
        $fooModel = app($path . '\\' . $model_name);
        $item = $fooModel::find($model_id);

        $uploaded_files = [];
        if (!empty($item)) {
            if (!empty($collection_name)) {
                $uploaded_files = $item->getMedia($collection_name);
            }
        }



        return view('general.view_uploaded_files')->with(compact(
            'uploaded_files'
        ));
    }

    public function switchLanguage($lang)
    {
        session()->put('language', $lang);

        return redirect()->back();
    }


    public function uploadImageTemp(Request $request)
    {
        $image = $request->image;



        //upload base64 image in laravel
        $image_name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        \Image::make($image)->save(public_path('temp/' . $image_name));




        return ['success' => true, 'url' => url('/temp/' . $image_name), 'filename' => $image_name];
    }

    public function uploadFileTemp(Request $request)
    {
        $data = $request->all();
        $file = $data['file'];

        $file_name = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('temp'), $file_name);;

        return ['success' => true, 'url' => url('/temp/' . $file_name), 'filename' => $file_name];
    }
}

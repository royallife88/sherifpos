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
        $fooModel = app($path . '\\'. $model_name);
        $item = $fooModel::find($model_id);

        $uploaded_files = [];
        if (!empty($item)) {
            if(!empty($collection_name)){
                $uploaded_files[] = $item->getFirstMediaUrl($collection_name);
            }
        }



        return view('general.view_uploaded_files')->with(compact(
            'uploaded_files'
        ));
    }
}

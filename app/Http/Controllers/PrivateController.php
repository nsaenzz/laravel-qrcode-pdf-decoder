<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrivateController extends Controller
{
    public function files($folder, $file){
        $path = "";
        if($folder != "main"){
            $path = "private" . DIRECTORY_SEPARATOR .  $folder . DIRECTORY_SEPARATOR . $file;
        }
        else{
            $path = "private" . DIRECTORY_SEPARATOR . $file;
        }

        if(Storage::exists($path)){
            $file = "app" . DIRECTORY_SEPARATOR . $path;
            $filepath = storage_path($file);
            return response()->file($filepath);
        }
        abort(404);
    }
}

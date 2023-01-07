<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class UploadController extends Controller
{
    public function upload(){
		$files = File::get();
		return view('upload',['file' => $files]);
	}

    public function store(Request $request){
        $this->validate($request, [
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file');
        $file_name = time()."_".$file->getClientOriginalName();

        $upload_dest = 'data_file';
        $file->move($upload_dest, $file_name);

        File::create([
            'file' => $file_name,
        ]);

        return redirect()->back();
    }
}

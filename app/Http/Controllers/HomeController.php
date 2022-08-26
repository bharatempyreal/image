<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function image(Request $request)
    {
        dd($request->all());
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $image->move(public_path('storage/app/public/uploads/'),$imageName);
        
        $imageUpload = new File();
        $imageUpload->filename = $imageName;
        $imageUpload->save();
        return response()->json(['success'=>$imageName]);
    }
    public function detory(Request $request)
    {
        $filename =  $request->get('filename');
        File::where('filename',$filename)->delete();
        $path=public_path().'/images/'.$filename;
        if (file_exists($path)) {
            unlink($path);
        }
        return $filename;
    }
}

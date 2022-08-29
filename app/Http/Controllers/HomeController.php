<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use DataTables;
use Storage;

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
        $image= File::all();
        return view('home', ['image'=> $image]);
    }
    public function image(Request $request)
    {
        $file = $request->file;
        // if($request->file){
            // foreach($request->file as $file){

                $path = storage_path('app/public/uploads/');
                $imageName=$file->getClientOriginalName();
                
                $file->move($path,$imageName);
                $imageUpload = new File();
                $imageUpload->name = $imageName;
                $imageUpload->save();
                // dd($insert_id);
            // }
        // }
       
        return response()->json(['success'=> true, 'id' => $imageUpload->id, 'image' => route('image.displayImage', $imageUpload->name)]);
    }
    public function remove(Request $request)
    {
        
        $post = File::find($request->id);

        File::where('id',$request->id)->delete();
        $path=storage_path('app/public/uploads/').$post;
       
        if (file_exists($path)) {
            unlink($path);
        }
        return $post;
    }

    public function table(Request $request)
    {
        $data = File::latest()->get();
        
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                        $btn =' <a href="javascript:void(0)" data-toggle="tooltip"  data-action="'.route('remove', $row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm delete">Delete</a>';
 
                         return $btn;
                    })
                    ->addColumn('image', function($row){
                        $image = '<img  src="'.route('image.displayImage', $row->name).  '" height="100px" width="100px">';
                        return $image;
                    })
                    ->rawColumns(['action','image'])
                    
                    ->make(true);
                    
    }
    function deleteImage(Request $request)
    {
        $filename =  $request->get('file');
        $post = File::find($filename);

        File::where('name',$filename)->delete();
        $path=storage_path('app/public/uploads/').$post;
        // dd($path);
        if (file_exists($path)) {
            unlink($path);
        }
        return $post;
    }

    public function getimg(Request $request)
    {
        $path = storage_path('app/public/uploads/');

        $image = File::latest()->get();


    
    return $image;

    }

}
    
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
        $images = File::latest()->get();
        return view('home', ['images'=> $images]);
        
    }
    public function image(Request $request)
    {
        // dd($request->productFiles);
        $file = $request->productFiles;
        if($request->productFiles){
            foreach($request->productFiles as $key => $file){

                $path = storage_path('app/public/uploads/');
                $imageName = $file->getClientOriginalName();

                $file->move($path,$imageName);
                $imageUpload = new File();
                $imageUpload->name = $imageName;
                $imageUpload->path = $key + 1;
                $imageUpload->save();
                // dd($insert_id);
            }
        }

        return response()->json(['success'=> true, 'path'=>$imageUpload->path, 'id' => $imageUpload->id, 'image' => route('image.displayImage', $imageUpload->name)]);
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
        $post = File::find($request->id);


        $path = storage_path('app/public/uploads') .'/'.$post->name;
        // dd(file_exists($path));
        if (file_exists($path)) {
            unlink($path);
        }
        File::where('id',$request->id)->delete();

        $return['success'] = true;
        $return['message'] = 'File deleted successfully. ';

        return $return;
    }

    public function getimg(Request $request)
    {

        
        if($request->ajax()){
            $images = File::orderBy('path', 'asc')->get();
            $result=[];
           

            foreach ($images as $image){
                $result[] = [
                    'id' => $image->id,
                    'name' => $image->name,
                    'server' => route("image.displayImage", $image->name),
                    'size' => Storage::disk('public')->size('uploads/'. $image->name),
                    // dd($size),
                    // 'size' =>filegetSize($image),
                ];

            }
            
            $return['success'] = true;
            $return['images'] = $result;

            return response()->json($return);
        }
    }
    public function sortable(Request $request)
    {
        // dd($request->all());
        if($request->ajax()){
            $images = File::orderBy('path', 'asc')->get();

                foreach($image as $key => $file){
    
                    $path = storage_path('app/public/uploads/');
                    $imageName = $file->getClientOriginalName();
    
                    $file->move($path,$imageName);
                    $imageUpload = new File();
                    $imageUpload->name = $imageName;
                    $imageUpload->path = $key + 1;
                    $imageUpload->save();
                    // dd($insert_id);
                }
        }
    }


}
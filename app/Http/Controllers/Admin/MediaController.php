<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Media;
use File; 

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchTerm = request()->get('s');
      $medias = Media::orWhere('title', 'LIKE', "%$searchTerm%")->latest()->paginate(15);
        return view('admin.media.index')
            ->with(compact('medias'));  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('admin.media.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'title' => 'required',
            'slug' => 'required',
            'media_type'=> 'required|not_in:none',
            'media_img' => 'required|mimes:png,jpg,jpeg,gif|max:2048'
        ]);

        $fileName = null;
        if (request()->hasFile('media_img')) 
        {
            $file = request()->file('media_img');
            $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $fileName);
        }



        media::create([
       'title' => request()->get('title'),
       'slug' => request()->get('slug'),
       'media_type' =>'media_type',
       'media_img' => $fileName,
       'description' => request()->get('description'), 
       'status' => 'DEACTIVE'
        ]);
        $notification = [
            'message' => 'Record Inserted Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->to('/admin/media')->with($notification);


    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $media = Media::find($id);  
      return view('admin.media.edit')
        ->with(compact('media'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
   {
        $media = Media::find($id);

        $currentImage = $media->media_img;

        $fileName = null;
            if (request()->hasFile('media_img')) 
            {
                $file = request()->file('media_img');
                $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
                $file->move('./uploads/', $fileName);
            }

        $media->update([
        'title' => request()->get('title'),
       'slug' => request()->get('slug'),
       'media_type' =>'media_type',
       'media_img' => ($fileName) ? $fileName : $currentImage,
       'description' => request()->get('description'), 
       'status' => 'DEACTIVE'
        ]);

        if ($fileName) 
            File::delete('./uploads/' . $currentImage);

        $notification = [
            'message' => 'Record Updated Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->to('/admin/media')->with($notification);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $media = Media::find($id);
      $currentImage = $media->media_img;
      $media->delete();
      File::delete('./uploads/' . $currentImage);
      $notification = [
            'message' => 'Record Deleted Successfully!',
            'alert-type' => 'success',
        ];
      return redirect()->back()->with($notification);
        }

    public function status($id)
    {
        $media = Media::find($id);
        $newStatus = ($media->status == 'DEACTIVE') ? 'ACTIVE' : 'DEACTIVE';
        $media->update([
            'status' => $newStatus
        ]);
        return redirect()->back();
    }
}

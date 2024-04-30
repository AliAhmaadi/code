<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Author; 
use File; 

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $authors = Author::get(); // without pagination
        $searchTerm = request()->get('s');
        $authors = Author::orWhere('title', 'LIKE', "%$searchTerm%")->latest()->paginate(15);
        return view('admin.author.index')
            ->with(compact('authors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.author.create');
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
            'designation' => 'required',
            'dob' => 'required',
            'email' => 'required',
            'country' => 'required|not_in:none',
        ]);

        $fileName = null;
        if (request()->hasFile('author_img')) {
            $file = request()->file('author_img');
            $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $fileName);
        }

        Author::create([
            'title' => request()->get('title'),
            'slug' => request()->get('slug'),
            'designation' => request()->get('designation'),
            'dob' => request()->get('dob'),
            'country' => request()->get('country'),
            'email' => request()->get('email'),
            'phone' => request()->get('phone'),
            'description' => request()->get('description'),
            'author_feature' => request()->get('author_feature'),
            'facebook_id' => request()->get('facebook_id'),
            'twitter_id' => request()->get('twitter_id'),
            'youtube_id' => request()->get('youtube_id'),
            'pinterest_id' => request()->get('pinterest_id'),
            'author_img' => $fileName,
            'status' => 'DEACTIVE',
        ]);

        $notification = [
            'message' => 'Record Inserted successfully',
            'alert-type' => 'success'
        ];

        return redirect()->to('/admin/author')
            ->with($notification);
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
        $author = Author::find($id);

        return view('admin.author.edit')
            ->with(compact('author'));
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
        $author = Author::find($id);
        
        $currentImage = $author->author_img;
        $fileName = null;
        if (request()->hasFile('author_img')) {
            $file = request()->file('author_img');
            $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $fileName);
        }
        $author->update([
            'title' => request()->get('title'),
            'slug' => request()->get('slug'),
            'designation' => request()->get('designation'),
            'dob' => request()->get('dob'),
            'country' => request()->get('country'),
            'email' => request()->get('email'),
            'phone' => request()->get('phone'),
            'description' => request()->get('description'),
            'author_feature' => request()->get('author_feature'),
            'facebook_id' => request()->get('facebook_id'),
            'twitter_id' => request()->get('twitter_id'),
            'youtube_id' => request()->get('youtube_id'),
            'pinterest_id' => request()->get('pinterest_id'),
            'author_img' => ($fileName) ? $fileName : $currentImage,
        ]);

        if ($fileName) {
            File::delete('./uploads/' . $currentImage);
        }
        $notification = [
            'message' => 'Record Updated successfully',
            'alert-type' => 'warning'
        ];
        return redirect()->to('/admin/author')
            ->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) 
        {
            $author = Author::find($id);
            $currentImage = $author->author_img;
            $author->delete();
            File::delete('./uploads/' . $currentImage);
            return 'true';
        }
            return redirect()->back();
    }

    public function status(Request $request, $id)
    {
        if ($request->ajax()) 
        {
            $author = Author::find($id);
            $newStatus = ($author->status == 'DEACTIVE') ? 'ACTIVE' : 'DEACTIVE';
            $author->update([
                'status' => $newStatus
            ]);
            return $newStatus;
        }
            return redirect()->back();
    }

    public function status_active(Request $request)
    {
        if ($request->ajax()) 
        {
            foreach ($request->statusAll as $key => $id) {
                Author::where('id', $id)->update(['status' => 'ACTIVE']);
            }
            $record = Author::find($request->statusAll);
            return $record;
        }
        return false;
    }

    public function status_deactive(Request $request)
    {
        if ($request->ajax()) 
        {
            foreach ($request->statusAll as $key => $id) {
                Author::where('id', $id)->update(['status' => 'DEACTIVE']);
            }
            $record = Author::find($request->statusAll);
            return $record;
        }
        return false;
    }

    public function delete_all(Request $request)
    {
        if ($request->ajax()) 
        {
            foreach ($request->statusAll as $key => $id) {
                Author::where('id', $id)->delete();
            }
            $record = Author::find($request->statusAll);
            return $record;
        }
        return false;
    }
}

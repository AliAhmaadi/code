<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Message;
use File; 
use App\Events\MessageSent;
use App\Events\Notify;
use Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchTerm = request()->get('s');
      $categorys = Category::orWhere('title', 'LIKE', "%$searchTerm%")->latest()->paginate(15);
        return view('admin.category.index')
            ->with(compact('categorys'));  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('admin.category.create');
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
        ]);

        Category::create([
        'title' => request()->get('title'),
        'slug' => request()->get('slug'),
        'description' => request()->get('description'), 
        'status' => 'DEACTIVE'
        ]);
        $notification = [
            'message' => 'Record Inserted Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->to('/admin/category')->with($notification);

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
      $category = Category::find($id);
        return view('admin.category.edit')
            ->with(compact('category'));
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
        $category = Category::find($id);
        $category->update([
        'title' => request()->get('title'),
        'slug' => request()->get('slug'),
        'description' => request()->get('description'), 
        'status' => 'DEACTIVE'
        ]);
        $notification = [
            'message' => 'Record Updated Successfully!',
            'alert-type' => 'success',
        ];
                // fire event
        $message = Message::create([
            'message' => 'Category: '.request()->get('title').' updated',
            'user_id' => Auth::id()
          ]);

        event(new Notify(Auth::user(), 'Category: '.request()->get('title').' updated'));
        
        return redirect()->to('/admin/category')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $category = Category::find($id);
      $category->delete();
      $notification = [
            'message' => 'Record Deleted Successfully!',
            'alert-type' => 'success',
        ];
      return redirect()->back()->with($notification);
        }

    public function status($id)
    {
        $category = Category::find($id);
        $newStatus = ($category->status == 'DEACTIVE') ? 'ACTIVE' : 'DEACTIVE';
        $category->update([
            'status' => $newStatus
        ]);
        return redirect()->back();
    }
}

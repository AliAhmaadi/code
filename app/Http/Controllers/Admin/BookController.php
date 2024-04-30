<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Book;
use App\Author;
use App\Category;
use App\Country;
use File;
use Auth;
use App\Events\Notify;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchTerm = request()->get('s');
      $books = Book::with('author', 'category')->orWhere('title', 'LIKE', "%$searchTerm%")->latest()->paginate(15);
        return view('admin.book.index')
            ->with(compact('books'));  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $authors = Author::where('status', 'ACTIVE')->get();
      $categories = Category::where('status', 'ACTIVE')->get();
      $countries = Country::get();
      return view('admin.book.create')
        ->with(compact('categories', 'authors', 'countries'));
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
            'category_id' => 'required|not_in:0',
            'author_id' => 'required|not_in:0',
            'availability' => 'required',
            'price' => 'required',
            'rating' => 'required',
            'country_of_publisher' => 'required|not_in:none',
            'book_img' => 'required|mimes:png,jpg,jpeg,gif|max:2048',
            'description' => 'required'
        ]);

        $fileName = null;
        if (request()->hasFile('book_img')) 
        {
            $file = request()->file('book_img');
            $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $fileName);
        }

        $PDFName = null;
        if (request()->hasFile('book_upload')) 
        {
            $file = request()->file('book_upload');
            $PDFName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $PDFName);
        }

        Book::create([
            'category_id' => request()->get('category_id'),
            'author_id' => request()->get('author_id'),
            'title' => request()->get('title'),
            'slug' => request()->get('slug'), 
            'availability' => request()->get('availability'), 
            'price' => request()->get('price'),
            'rating' => request()->get('rating'),
            'publisher' => request()->get('publisher'),
            'country_of_publisher' => request()->get('country_of_publisher'),
            'isbn' => request()->get('isbn'),
            'isbn_10' => request()->get('isbn_10'),
            'audience' => request()->get('audience'),
            'format' => request()->get('format'),
            'language' => request()->get('language'),
            'total_pages' => request()->get('total_pages'),
            'downloaded' => request()->get('downloaded'),
            'edition_number' => request()->get('edition_number'),
            'recommended' => request()->get('recommended'),
            'description' => request()->get('description'),
            'book_img' => $fileName, 
            'book_upload' => $PDFName,
            'status' => 'DEACTIVE'
        ]);
        $notification = [
            'message' => 'Record Inserted Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->to('/admin/book')->with($notification);


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
      $book = Book::find($id); 
      $authors = Author::where('status', 'ACTIVE')->get();
      $categories = Category::where('status', 'ACTIVE')->get(); 
      return view('admin.book.edit')
        ->with(compact('book', 'categories', 'authors'));
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
        $book = Book::find($id);

        $currentImage = $book->book_img;
        $currentBook = $book->book_upload;

        $PDFName = null;
        if (request()->hasFile('book_upload')) 
        {
            $file = request()->file('book_upload');
            $PDFName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/', $PDFName);
        }

        $book->update([
            'category_id' => request()->get('category_id'),
            'author_id' => request()->get('author_id'),
            'title' => request()->get('title'),
            'slug' => request()->get('slug'), 
            'availability' => request()->get('availability'), 
            'price' => request()->get('price'),
            'rating' => request()->get('rating'),
            'publisher' => request()->get('publisher'),
            'country_of_publisher' => request()->get('country_of_publisher'),
            'isbn' => request()->get('isbn'),
            'isbn_10' => request()->get('isbn_10'),
            'audience' => request()->get('audience'),
            'format' => request()->get('format'),
            'language' => request()->get('language'),
            'total_pages' => request()->get('total_pages'),
            'downloaded' => request()->get('downloaded'),
            'edition_number' => request()->get('edition_number'),
            'recommended' => request()->get('recommended'),
            'description' => request()->get('description'),
            'book_img' =>  ($fileName) ? $fileName : $currentImage, 
            'book_upload' => ($PDFName) ? $PDFName : $currentBook,
            'status' => 'DEACTIVE'
        ]);

        if ($fileName ) 
            File::delete('./uploads/' . $currentImage);
        if ($PDFName ) 
            File::delete('./uploads/' . $currentBook);

        $notification = [
            'message' => 'Record Update Successfully!',
            'alert-type' => 'success',
        ];
        return redirect()->to('/admin/book')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $book = Book::find($id);
      $currentImage = $book->book_img;
      $book->delete();
      File::delete('./uploads/' . $currentImage);
      $notification = [
            'message' => 'Record Deleted Successfully!',
            'alert-type' => 'success',
        ];
      return redirect()->back()->with($notification);
        }


    public function status($id)
    {
        $book = Book::find($id);
        $newStatus = ($book->status == 'DEACTIVE') ? 'ACTIVE' : 'DEACTIVE';
        $book->update([
            'status' => $newStatus
        ]);
        return redirect()->back();
    }
}

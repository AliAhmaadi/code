<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Book;

class CategoryController extends Controller
{
  public function detail($slug)
  {
  	$category = Category::where('slug', $slug)->first();
  	$books = Book::where('category_id', $category->id)->get();
  	$categories = Category::get();
  	return view('category.detail', compact('books', 'categories'));
  }
}

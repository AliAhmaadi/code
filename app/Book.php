<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
  protected $table = 'book';
  protected $guarded = [];

  public function author()
  {
  	return $this->belongsTo(Author::class, 'author_id');
  }

  public function category()
  {
  	return $this->belongsTo(Category::class, 'category_id');
  }
}

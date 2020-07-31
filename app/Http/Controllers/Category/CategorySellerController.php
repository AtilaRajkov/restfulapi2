<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategorySellerController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Category $category)
  {
    $sellers = $category->products()
      ->with('seller')
      ->get()
      ->pluck('seller')
      ->unique('id')
      ->values();

    //return response()->json(['count' => $sellers->count(), 'data' => $sellers], 200);
    return $this->showAll($sellers);
  }


}

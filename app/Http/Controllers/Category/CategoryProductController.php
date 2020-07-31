<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategoryProductController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Category $category)
  {
    $products = $category->products;

    //return response()->json(['count' => $products->count(),'data' => $products], 200);
    return $this->showAll($products);
  }


}

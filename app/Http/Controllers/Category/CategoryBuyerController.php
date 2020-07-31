<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategoryBuyerController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Category $category)
  {
    $buyers = $category->products()
      ->whereHas('transactions')
      ->with('transactions.buyer')
      ->get()
      ->pluck('transactions')
      ->collapse()
      ->pluck('buyer')
      ->unique('id')
      ->values();

    //return response()->json(['count'=>$buyers->count(),'data' => $buyers], 200);
    return $this->showAll($buyers);
  }

}

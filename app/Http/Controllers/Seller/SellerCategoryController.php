<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerCategoryController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Seller $seller)
  {
    $categories = $seller->products()
      ->whereHas('categories')
      ->with('categories')
      ->get()
      ->pluck('categories')
      ->collapse()
      ->unique('id')
      ->values();

    //return response()->json(['data' => $categories], 200);
    return $this->showAll($categories);
  }


}

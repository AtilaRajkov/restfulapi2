<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //$sellers = Seller::has('products')->get();
    $sellers = Seller::all();
    //return response()->json(['data' => $sellers], 200);
    return $this->showAll($sellers, 200);
  }

  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function show(Seller $seller)
  {
    //$seller = Seller::has('products')->findOrFail($id);
    //return response()->json(['data' => $seller], 200);
    return $this->showOne($seller, 200);
  }
}

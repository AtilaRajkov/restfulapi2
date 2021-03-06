<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //$buyers = Buyer::has('transactions')->get();
    $buyers = Buyer::all();
    //return response()->json(['data' => $buyers], 200);
    return $this->showAll($buyers, 200);
  }


  /**
   * Display the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function show(Buyer $buyer)
  {
    //$buyer = Buyer::has('transactions')->findorFail($id);
    //return response()->json(['data' => $buyer], 200);
    return $this->showOne($buyer, 200);
  }

}

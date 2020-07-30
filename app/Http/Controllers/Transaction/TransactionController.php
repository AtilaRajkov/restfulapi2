<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $transactions = Transaction::all();
    //return response()->json(['data' => $transactions], 200);
    return $this->showAll($transactions);
  }

  /**
   * Display the specified resource.
   *
   * @param \App\Transaction $transaction
   * @return \Illuminate\Http\Response
   */
  public function show(Transaction $transaction)
  {
    //return response()->json(['data' => $transaction], 200);
    return $this->showOne($transaction);
  }


}

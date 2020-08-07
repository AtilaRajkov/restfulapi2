<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\User;
use Faker\Provider\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Seller $seller)
  {
    $products = $seller->products;

    //return response()->json(['data' => $products], 200);
    return $this->showAll($products);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, User $seller)
  {
    $rules = [
      'name' => 'required|string|min:2|max:191|unique:products',
      'description' => 'required|string',
      'quantity' => 'required|integer|min:1',
      'image' => 'required|image'
    ];

    //$this->validate($request, $rules);
    //$data = $request->all();
    $data = request()->validate($rules);

    $data['status'] = Product::UNAVAILABLE_PRODUCT;
    $data['image'] = $request->image->store('');
    $data['seller_id'] = $seller->id;

    $product = Product::create($data);

    //return response()->json(['data' => $product]);
    return $this->showOne($product);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Seller $seller
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Seller $seller, Product $product)
  {
    $rules = [
//      'name' => 'required',
//      'description' => 'required',
      'quantity' => 'required|min:1',
      'status' => 'in:'.Product::AVAILABLE_PRODUCT.','.Product::UNAVAILABLE_PRODUCT,
      'image' => 'image',
    ];

    //$this->validate($request, $rules);
    //$data = $request->all();
    $data = request()->validate($rules);

    $this->checkSeller($seller, $product);

    $product->fill(request()->only([
      'name',
      'description',
      'status'
    ]));

    if (request()->has('status')) {
      $product->status = request('status');

      if ($product->isAvailable() && $product->categories()->count() == 0) {
        return $this->errorResponse('An active product must have at least one catrgory', 409);
      }
    }

    if ($request->hasFile('image')) {
      Storage::delete($product->image);
      $product->image = $request->image->store('');
    }

    if ($product->isClean()) {
      return $this->errorResponse('You need to specify a different value to update', 409);
    }

    $product->save();

    //return response()->json(['data' => $product], 201);
    return $this->showOne($product);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Seller $seller
   * @return \Illuminate\Http\Response
   */
  public function destroy(Seller $seller, Product $product)
  {
    $this->checkSeller($seller, $product);

    Storage::delete($product->image);

    $product->delete();

    //return response()->json(['data' => $product], 200);
    return $this->showOne($product);
  }

  protected function checkSeller(Seller $seller, Product $product)
  {
    if ($seller->id != $product->seller_id) {
      throw new HttpException(422, "The specified seller is not the actual seller of the product.");
    }
  }
}

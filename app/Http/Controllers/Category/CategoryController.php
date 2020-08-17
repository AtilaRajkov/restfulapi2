<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
  public function __construct()
  {
    parent::__construct();

    $this->middleware('transform.input:'.CategoryTransformer::class)
      ->only(['store', 'update']);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $categories = Category::all();
    //return response()->json(['data' => $categories], 200);
    return $this->showAll($categories);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|string|min:2|max:191|unique:categories',
      'description' => 'required|string|min:2|max:1000'
    ];

    $data = request()->validate($rules);

    $category = Category::create($data);

    //return response()->json(['data' => $category], 201);
    return $this->showOne($category, 201);
  }


  /**
   * Display the specified resource.
   *
   * @param \App\Category $category
   * @return \Illuminate\Http\Response
   */
  public function show(Category $category)
  {
    //return response()->json(['data' => $category]);
    return $this->showOne($category);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Category $category
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Category $category)
  {
    $category->fill(request()->only([
      'name',
      'description'
    ]));

    //if (!$category->isDirty()) {
    if ($category->isClean()) {
      return $this->errorResponse(
        'You need to use a different value to be able tu update',
        422);
    }

    $category->save();

    //return response()->json(['data' => $category], 200);
    return $this->showOne($category);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Category $category
   * @return \Illuminate\Http\Response
   */
  public function destroy(Category $category)
  {
    $category->delete();
    //return response()->json(['data' => $category], 200);
    return $this->showOne($category);
  }
}

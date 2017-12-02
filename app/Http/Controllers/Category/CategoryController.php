<?php

namespace App\Http\Controllers\category;

use App\category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class CategoryController extends ApiController
{
    public function __construct()
    {
              
        $this->middleware('client.credentials')->only(['index','show']);    
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store','update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();

        return  $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->allowedAdminAction();
        $rules = [
            'name'=> 'required',
            'description'=>'required'
        ];

        $this->validate($request, $rules);
        $newCategory = Category::create($request->all());

        return $this->showOne($newCategory,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
       return  $this->showOne($category);
    }

  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->allowedAdminAction();

        $category->fill($request->only(
            'name',
            'description'
        ));

        if($category->isClean())
        {
            return $this->errorResponse('You need to specify any different value for update',422);
        }

        $category->save();

        return $this->showOne($category);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->allowedAdminAction();
        
        $category->delete();

        return $this->showOne($category);
        
    }
}

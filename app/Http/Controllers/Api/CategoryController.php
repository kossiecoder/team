<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
	public function index()
	{
        $no_per_page = config('custom.no_per_page');
	    $categories = new Category();

	    if (request('search')) {
            $categories = $categories->where('title', 'LIKE', '%' . request('search') . '%');
        }

	    if (request('page')) {
	        $categories = $categories->paginate($no_per_page);
        } else {
            $categories = $categories->get();
        }

		return response()->json([
			'categories' => $categories
		], Response::HTTP_OK);
    }

    public function show(Category $category)
    {
        return response()->json([
            'category' => $category
        ], Response::HTTP_OK);
    }

	public function store(CategoryStoreRequest $request)
	{
		$category = Category::create($request->validated());

		return response()->json([
			'category' => $category
		], Response::HTTP_CREATED);
    }

	public function update(CategoryUpdateRequest $request, Category $category)
	{
		$category->update($request->validated());

		return response()->json([
			'category' => $category
		],Response::HTTP_OK);
    }

    public function destroy(Category $category)
	{
		$category->delete();

		return response()->json([
			'category' => $category
		], Response::HTTP_OK);
    }
}

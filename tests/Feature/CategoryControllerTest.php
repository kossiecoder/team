<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
	use WithFaker, RefreshDatabase;
	
    /**
     * GET: /api/categories
     * @test
     */
	public function canGetCategories()
	{
		$user = factory(User::class)->create();
	
		$this->actingAs($user, 'api');
		
		$response = $this->json('GET', '/api/categories');
		
		$response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['categories']);
    }
    
    /**
     * POST: /api/categories
     * @test
     */
	public function canCreateCategory()
	{
		$user = factory(User::class)->create();
		
		$this->actingAs($user, 'api');
		
		$title = $this->faker->sentence;
		$description = $this->faker->paragraph;
		
		$response = $this->json('POST', '/api/categories', [
			'title'         => $title,
			'description'   => $description
		]);
		
		$response->assertStatus(Response::HTTP_CREATED)->assertJsonStructure(['category']);
		
		$this->assertDatabaseHas('categories', [
			'title'         => $title,
			'description'   => $description
		]);
    }
    
    /**
     * PUT: /api/categories/{category}
     * @test
     */
	public function canUpdateCategory()
	{
		$this->actingAs(factory(User::class)->create(), 'api');
		
		$category = factory(Category::class)->create();
		
		$newTitle = $this->faker->sentence;
		$newDescription = $this->faker->paragraph;
		
		$response = $this->json('PUT', '/api/categories/'.$category->id, [
			'title'         => $newTitle,
			'description'   => $newDescription
		]);
		
		$response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['category']);
		
		$this->assertDatabaseHas('categories', [
			'id'            => $category->id,
			'title'         => $newTitle,
			'description'   => $newDescription
		]);
    }
    
    /**
     * DELETE: /api/categories/{category}
     * @test
     */
	public function canDeleteCategory()
	{
		$this->actingAs(factory(User::class)->create(), 'api');
		
		$category = factory(Category::class)->create();
		
		$response = $this->json('DELETE', '/api/categories/'.$category->id);
		
		$response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['category' => ['title', 'description']]);
		
		$this->assertDatabaseMissing('categories', [
			'id'            => $category->id,
			'title'         => $category->title,
			'description'   => $category->description
		]);
    }
}

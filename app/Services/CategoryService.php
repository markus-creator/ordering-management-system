<?php

namespace App\Services;

use App\Models\Category;
use App\Services\Interfaces\CategoryServiceInterface;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryService implements CategoryServiceInterface
{
	public function get(object $request): QueryBuilder
	{
		try {
			// set model
			$model = Category::query()
			  ->withTrashed()
			  ->search($request->search);

			// set query builder
			$query = QueryBuilder::for($model)
			  ->defaultSort('created_at')
			  ->allowedSorts(['name', 'description', 'created_at'])
			  ->allowedFilters(['name', 'description']);

			return $query;
		} catch (\Exception $e) {
			(new FlashNotification())->error($e->getMessage());
		}
	}

   public function store(object $request): void
   {
   	try {
   		\DB::transaction(function () use ($request) {
   			$permissions = collect($request->permissions)->pluck('name');

   			$category = Category::make(
   				[
   					'name' => $request->name,
   					'description' => $request->description,
   					'guard_name' => 'web',
   				]
   			);

   			$category->givePermissionTo($permissions);
   			$category->saveOrFail();
   		});
   	} catch (\Exception $e) {
   		(new FlashNotification())->error($e->getMessage());
   	}
   }

   public function update(object $request, string $id): void
   {
   	try {
   		$category = Category::findOrFail($id);
   		\DB::transaction(
   			function () use ($request, $category) {
   				$permissions = collect($request->permissions)->pluck('name');

   				$category->update([
   					'name' => $request->name,
   					'description' => $request->description,
   				]);

   				$category->syncPermissions($permissions);
   			}
   		);
   	} catch (\Exception $e) {
   		(new FlashNotification())->error($e->getMessage());
   	}
   }

   public function delete(string $id): void
   {
   	try {
   		Category::findOrFail($id)->delete();
   	} catch (\Exception $e) {
   		(new FlashNotification())->error($e->getMessage());
   	}
   }

  public function deleteMultiple(array $ids): void
  {
  	try {
  		\DB::transaction(function () use ($ids) {
  			Category::whereIn('id', $ids)->get()->each->delete();
  		});
  	} catch (\Exception $e) {
  		(new FlashNotification())->error($e->getMessage());
  	}
  }

  public function restore(string $id): void
  {
  	try {
  		Category::onlyTrashed()->findOrFail($id)->restore();
  	} catch (\Exception $e) {
  		(new FlashNotification())->error($e->getMessage());
  	}
  }

  public function retoreMultiple(array $ids): void
  {
  	try {
  		\DB::transaction(function () use ($ids) {
  			Category::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
  		});
  	} catch (\Exception $e) {
  		(new FlashNotification())->error($e->getMessage());
  	}
  }
}

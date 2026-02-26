<?php

namespace Modules\CourseManagement\Services;

use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CourseManagement\Models\Category;

class CategoryService {

    use FilterableServiceTrait;

    /**
     * Summary of cacheKey
     * @param array $filters
     * @return string
     */
    protected function cacheKey(array $filters = []): string {
        ksort($filters);

        return NameOfCache::Category->value . '_' . md5(json_encode($filters));
    }
    /**
     * Summary of getAll
     * @param mixed $filters
     */
    public  function getAll($filters) {

        return Cache::tags([NameOfCache::Category->value])
        ->remember($this->cacheKey($filters), now()->addDay(), function () use ($filters) {
            $categories = Category::query()->with(['parent','media']);
            return $this->applyFilters($categories, $filters);
        });
    }
    /**
     * Summary of makeCategory
     * @param array $data
     */
    public function makeCategory(array $data) {
        try {
            DB::beginTransaction();
            $category = Category::create($data);
            if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
                $category->addMedia($data['image'])->toMediaCollection('category');
            }
            DB::commit();
            Cache::tags([NameOfCache::Category->value])->flush();
            return $category->load(['parent', 'media']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Fail To Make Category ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Summary of getCategory
     * @param Model $model
     * @return Model
     */
    public function getCategory(Model $model) {
        
        return $model->load(['parent','media']);
    }
    /**
     * Summary of updateCategory
     * @param Model $model
     * @param array $data
     * @throws HttpClientException
     * @return Model
     */
    public function updateCategory(Model $model, array $data) {
        try {
            DB::beginTransaction();
            $success = $model->update($data);
            if (!$success) {
                throw new \RuntimeException('Category update failed');
            }
            if (isset($data['image'])) {
                if ($model->hasMedia('category')) {
                    $model->clearMediaCollection('category');
                }
                $model->addMedia($data['image'])->toMediaCollection('category');
            }
            DB::commit();
            Cache::tags([NameOfCache::Category->value])->flush();
            return $model->load(['parent','media']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Fail To Update Category ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of deleteCategory
     * @param Model $model
     * @return bool
     */
    public function deleteCategory(Model $model): bool {
        DB::transaction(function () use ($model) {
            $model->delete();
        },5);
        Cache::tags([NameOfCache::Category->value])->flush();
        return true;
    }
}

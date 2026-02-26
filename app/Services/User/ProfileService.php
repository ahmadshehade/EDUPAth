<?php

namespace App\Services\User;

use App\Enums\NameOfCache;
use App\Enums\NameOfCahce;
use App\Models\Profile;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileService {

    use FilterableServiceTrait;


    /**
     * Summary of cacheKey
     * @param mixed $filters
     * @return string
     */
    protected function cacheKey($filters){
        return NameOfCache::Profiles->value.md5(json_encode($filters));
    }
    /**
     * Summary of getAll
     * @param array $filters
     */
    public function getAll(array $filters = []) {
        return Cache::tags([NameOfCache::Profiles->value])->remember($this->cacheKey($filters), now()->addMinutes(2), function () use ($filters) {
            $profiles = Profile::query()->with(['user','media']);
            return $this->applyFilters($profiles, $filters);
        });
    }
    /**
     * Summary of store
     * @param array $data
     * @return Profile
     */
    public function store(array $data) {
        try {
            DB::beginTransaction();
            $data['user_id'] = auth('api')->user()->id;
            if(auth('api')->user()->profile){
                throw new HttpClientException("User Already Have Profile.",500);
            }
            $profile = Profile::create($data);
            Cache::tags([NameOfCache::Profiles->value])->flush();
            $image = $data['image'] ?? null;
            if ($image instanceof UploadedFile && $image->isValid()) {
                $profile->addMedia($image)->toMediaCollection('profile');
            }
            DB::commit();
            return $profile->load(['user','media']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Fail To Make Profile .' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Summary of get
     * @param Model $profile
     * @return Model|\stdClass
     */
    public function get(Model $profile) {
        $data = $profile->with('user')->firstOrFail();
        return $data;
    }
    /**
     * Summary of update
     * @param Model $profile
     * @param array $data
     * @throws Exception
     * @return Model
     */
    public function update(Model $profile, array $data) {
        try {
            DB::beginTransaction();
            $updated = $profile->update($data);
            if (!$updated) {
                throw new Exception('Failed to update profile');
            }
            $image = $data['image'] ?? null;
            if ($image && $image instanceof UploadedFile && $image->isValid()) {
                if ($profile->hasMedia('profile')) {
                    $profile->getFirstMedia('profile')->delete();
                }
                $profile->addMedia($image)->toMediaCollection('profile');
            }
            DB::commit();
            Cache::tags([NameOfCache::Profiles->value])->flush();
            return $profile->load(['user','media']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Fail To Update Profile ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of destroy
     * @param Model $profile
     * @return bool|int|mixed|null
     */
    public function destroy(Model $profile) {
        try {
            DB::beginTransaction();
            if ($profile->hasMedia('profile')) {
                $profile->getFirstMedia('profile')->delete();
            }
            $success = $profile->delete();
            Cache::tags([NameOfCache::Profiles->value])->flush();
            DB::commit();
            return $success;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Fail To Delete Profile: ' . $e->getMessage());
            throw $e;
        }
    }
}

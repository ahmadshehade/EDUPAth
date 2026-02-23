<?php

namespace App\Services\User;

use App\Enums\NameOfCahce;
use App\Models\Profile;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProfileService {

    use FilterableServiceTrait;

    /**
     * Summary of getAll
     * @param array $filters
     */
    public function getAll(array $filters = []) {
        return Cache::remember(NameOfCahce::Profiles->value, now()->addMinutes(2), function () use ($filters) {
            $profiles = Profile::query();
            return $this->applyFilters($profiles, $filters)->with(['user']);
        });
    }
    /**
     * Summary of store
     * @param array $data
     * @return Profile
     */
    public function store(array $data) {
        try {
            $data['user_id'] = auth('api')->user()->id;
            $profile = Profile::create($data);
            Cache::forget(NameOfCahce::Profiles->value);
            return $profile->load('user');
        } catch (Exception $e) {
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
        $data = $profile->with('users')->firstOrFail();
        return $data;
    }
    /**
     * Summary of update
     * @param Model $profile
     * @param array $data
     * @return bool|int
     */
    public function update(Model $profile, array $data) {
        try {
            $dataUpdated = $profile->update($data);
            Cache::forget(NameOfCahce::Profiles->value);
            return  $dataUpdated;
            
        } catch (Exception $e) {
            Log::error('Fail To Update Profile .' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Summary of destroy
     * @param Model $profile
     * @return bool|int|mixed|null
     */
    public function destroy(Model $profile) {
        $success=$profile->delete();
         Cache::forget(NameOfCahce::Profiles->value);
        return $success;
    }
}

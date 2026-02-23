<?php

namespace App\Services\User;

use App\Enums\NameOfCahce;
use App\Models\User;
use App\Traits\FilterableServiceTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserService {

    use FilterableServiceTrait;

    /**
     * Summary of getAll
     * @param array $filters
     */
    public  function getAll(array $filters = []) {

        $cacheKey = NameOfCahce::Users->value;
        return Cache::remember($cacheKey, now()->addDay(), function () use ($filters) {
            $users = User::query();
            return $this->applyFilters($users, $filters);
        });
    }
    /**
     * Summary of getUser
     * @param Model $user
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>|\Illuminate\Support\Collection<int, \stdClass>
     */
    public function getUser(Model $user) {
        $data = $user->with(['roles', 'permissions'])->get();
        return $data;
    }
    /**
     * Summary of update
     * @param Model $user
     * @param array $data
     * @return bool|int
     */
    public  function update(Model  $user, array $data) {
        try {
            $data = $user->update($data);
             Cache::forget(NameOfCahce::Users->value);
            return $data;
        } catch (Exception $e) {
            Log::error('Fail To Update User Information .' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Summary of destroy
     * @param Model $user
     * @return bool|int|mixed|null
     */
    public function destroy(Model $user) {

        $success = $user->delete();
        Cache::forget(NameOfCahce::Users->value);
        return $success;
    }
}

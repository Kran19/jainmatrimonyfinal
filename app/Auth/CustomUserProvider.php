<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class CustomUserProvider implements UserProvider
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function retrieveById($identifier)
    {
        return $this->createModelQuery()->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModelQuery()->find($identifier);

        if (! $model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return null;
        }

        $query = $this->createModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        // 1. Attempt standard password verification (bcrypt)
        if (password_verify($plain, $user->getAuthPassword())) {
            return true;
        }

        // 2. Fallback check: if user has not set a password, compare registered mobile number (last 10 digits match)
        if (!$user->has_set_password) {
            $cleanedUserMobile = preg_replace('/[^0-9]/', '', $user->mobile);
            $cleanedInputPassword = preg_replace('/[^0-9]/', '', $plain);

            if (strlen($cleanedUserMobile) >= 10 && strlen($cleanedInputPassword) >= 10) {
                $userLast10 = substr($cleanedUserMobile, -10);
                $inputLast10 = substr($cleanedInputPassword, -10);

                if ($userLast10 === $inputLast10) {
                    return true;
                }
            }
        }

        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return false;
    }

    protected function createModelQuery()
    {
        return app($this->model)->newQuery();
    }
}

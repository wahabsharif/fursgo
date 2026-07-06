<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Lean credential lookup for groomer/spacer login — avoids loading large JSON columns during Auth::attempt().
 */
class GroomerSpacerUserProvider extends EloquentUserProvider
{
    /**
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if ($credentials === [] || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains((string) $key, 'password')) {
                continue;
            }

            $query->where($key, $value);
        }

        /** @var Authenticatable|null $user */
        $user = $query->first(['id', 'email', 'password', 'remember_token']);

        return $user;
    }
}

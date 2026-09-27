<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SequenceService
{
    /**
     * Renvoie la valeur suivante de la séquence `$key`, sans doublon même en
     * accès concurrent (ligne verrouillée dans une transaction).
     *
     * `$initial` fournit la dernière valeur déjà utilisée quand la séquence
     * n'existe pas encore (reprise des numéros existants).
     */
    public function next(string $key, ?callable $initial = null): int
    {
        return DB::transaction(function () use ($key, $initial) {
            DB::table('sequences')->insertOrIgnore([
                'key' => $key,
                'last_value' => $initial ? (int) $initial() : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $current = (int) DB::table('sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->value('last_value');

            $next = $current + 1;

            DB::table('sequences')
                ->where('key', $key)
                ->update(['last_value' => $next, 'updated_at' => now()]);

            return $next;
        });
    }

    /**
     * Plus grand suffixe numérique parmi les valeurs de `$column` commençant par `$prefix`.
     */
    public static function maxSuffix(string $table, string $column, string $prefix): int
    {
        return (int) DB::table($table)
            ->where($column, 'like', $prefix.'%')
            ->pluck($column)
            ->map(fn (string $value) => (int) substr($value, strlen($prefix)))
            ->max();
    }
}

<?php

namespace Tests\Feature\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Réversibilité de la migration « caisse par campus ». Classe à part, sans données :
 * sous MySQL, une modification de structure valide la transaction du test en cours.
 */
class CampusMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_columns_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_09_28_090000_add_campus_to_accounting.php');
        $migration->down();
        foreach (['cash_transactions', 'cash_closings', 'students', 'users'] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'place_id'), $table);
        }
        $this->assertFalse(Schema::hasColumn('places', 'code'));

        $migration->up();
        foreach (['cash_transactions', 'cash_closings', 'students', 'users'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'place_id'), $table);
        }
    }
}

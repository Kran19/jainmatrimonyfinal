<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Test database connection.
     */
    public function test_database_connection(): void
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $this->assertNotEmpty($tables);
            
            // Check if users table exists
            $hasUsersTable = false;
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                if ($tableName === 'users') {
                    $hasUsersTable = true;
                    break;
                }
            }
            $this->assertTrue($hasUsersTable, 'The database does not contain a "users" table.');
        } catch (\Exception $e) {
            $this->fail('Database connection failed: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersMakeEmailNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'email' => [
                'name' => 'email',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        
        // Ensure phone is unique
        try {
            // Note: addKey doesn't add unique to an existing table column reliably without raw SQL in some DBs.
            $this->db->query('ALTER TABLE users ADD UNIQUE INDEX IF NOT EXISTS phone (phone)');
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }

    public function down()
    {
        // Revert email back to not null (not strictly necessary for rollback but good practice)
        $this->forge->modifyColumn('users', [
            'email' => [
                'name' => 'email',
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }
}

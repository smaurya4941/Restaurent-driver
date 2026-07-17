<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationAddressToVisits extends Migration
{
    public function up()
    {
        $this->forge->addColumn('visits', [
            'location_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Reverse-geocoded human-readable address',
                'after'      => 'location_captured_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('visits', 'location_address');
    }
}

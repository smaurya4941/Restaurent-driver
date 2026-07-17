<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGeolocationToVisits extends Migration
{
    public function up()
    {
        $fields = [
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'after'      => 'remarks',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'after'      => 'latitude',
            ],
            'location_accuracy' => [
                'type'       => 'DECIMAL',
                'constraint' => '9,2',
                'null'       => true,
                'comment'    => 'GPS accuracy radius in meters',
                'after'      => 'longitude',
            ],
            'location_captured_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'location_accuracy',
            ],
        ];

        $this->forge->addColumn('visits', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('visits', [
            'latitude',
            'longitude',
            'location_accuracy',
            'location_captured_at',
        ]);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultCashIncentiveToDrivers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('drivers', [
            'default_cash_incentive_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 200.00,
                'after'      => 'notes',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('drivers', 'default_cash_incentive_amount');
    }
}

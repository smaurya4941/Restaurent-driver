<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDriverBonusAwards extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'driver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'incentive_rule_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'month' => ['type' => 'TINYINT', 'constraint' => 2, 'unsigned' => true],
            'year' => ['type' => 'SMALLINT', 'constraint' => 4, 'unsigned' => true],
            'visit_threshold' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_visits' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'total_guests' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'total_cash_incentive' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bonus_basis_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bonus_percentage' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'bonus_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'computed_at' => ['type' => 'DATETIME', 'null' => true],
            'payout_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'eligible'],
            'approved_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'paid_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'payout_reference' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payout_notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('driver_id');
        $this->forge->addKey('incentive_rule_id');
        $this->forge->addUniqueKey(['driver_id', 'incentive_rule_id', 'year', 'month'], 'driver_bonus_awards_unique_rule_month');
        $this->forge->addForeignKey('driver_id', 'drivers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('incentive_rule_id', 'incentive_rules', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('driver_bonus_awards', true);
    }

    public function down()
    {
        $this->forge->dropTable('driver_bonus_awards', true);
    }
}

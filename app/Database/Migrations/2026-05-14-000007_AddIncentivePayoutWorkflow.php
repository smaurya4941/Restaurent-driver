<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIncentivePayoutWorkflow extends Migration
{
    public function up()
    {
        $fields = [
            'payout_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'not_eligible',
                'after'      => 'computed_at',
            ],
            'approved_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'payout_status',
            ],
            'approved_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'approved_by_user_id',
            ],
            'paid_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'approved_at',
            ],
            'paid_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'paid_by_user_id',
            ],
            'payout_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'paid_at',
            ],
            'payout_notes' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'payout_reference',
            ],
        ];

        $this->forge->addColumn('driver_monthly_summaries', $fields);
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD KEY `driver_monthly_summaries_approved_by_user_id_index` (`approved_by_user_id`)');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD KEY `driver_monthly_summaries_paid_by_user_id_index` (`paid_by_user_id`)');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD CONSTRAINT `driver_monthly_summaries_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD CONSTRAINT `driver_monthly_summaries_paid_by_user_id_foreign` FOREIGN KEY (`paid_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP FOREIGN KEY `driver_monthly_summaries_approved_by_user_id_foreign`');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP FOREIGN KEY `driver_monthly_summaries_paid_by_user_id_foreign`');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP KEY `driver_monthly_summaries_approved_by_user_id_index`');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP KEY `driver_monthly_summaries_paid_by_user_id_index`');
        $this->forge->dropColumn('driver_monthly_summaries', [
            'payout_status',
            'approved_by_user_id',
            'approved_at',
            'paid_by_user_id',
            'paid_at',
            'payout_reference',
            'payout_notes',
        ]);
    }
}

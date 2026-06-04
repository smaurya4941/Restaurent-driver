<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeIncentiveEngine extends Migration
{
    public function up()
    {
        $this->addIncentiveRuleVersioningIndexes();
        $this->upgradeDriverMonthlySummaries();
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP FOREIGN KEY `driver_monthly_summaries_incentive_rule_id_foreign`');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP KEY `driver_monthly_summaries_incentive_rule_id_index`');

        $this->forge->dropColumn('driver_monthly_summaries', [
            'incentive_rule_id',
            'total_amount_paid',
            'computed_at',
        ]);

        $this->db->query('ALTER TABLE `incentive_rules` ADD KEY `incentive_rules_visit_threshold_index` (`visit_threshold`)');
        $this->db->query('ALTER TABLE `incentive_rules` ADD KEY `incentive_rules_is_active_index` (`is_active`)');
    }

    private function addIncentiveRuleVersioningIndexes(): void
    {
        $this->db->query('ALTER TABLE `incentive_rules` DROP KEY `visit_threshold`');
        $this->db->query('ALTER TABLE `incentive_rules` DROP KEY `is_active`');
        $this->db->query('ALTER TABLE `incentive_rules` ADD KEY `incentive_rules_active_effective_index` (`is_active`, `effective_from`, `effective_to`)');
        $this->db->query('ALTER TABLE `incentive_rules` ADD KEY `incentive_rules_threshold_effective_index` (`visit_threshold`, `effective_from`, `effective_to`)');
    }

    private function upgradeDriverMonthlySummaries(): void
    {
        $fields = [
            'incentive_rule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'driver_id',
            ],
            'total_amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'after'      => 'total_cash_incentive',
            ],
            'computed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'bonus_amount',
            ],
        ];

        $this->forge->addColumn('driver_monthly_summaries', $fields);
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD KEY `driver_monthly_summaries_incentive_rule_id_index` (`incentive_rule_id`)');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD CONSTRAINT `driver_monthly_summaries_incentive_rule_id_foreign` FOREIGN KEY (`incentive_rule_id`) REFERENCES `incentive_rules`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }
}

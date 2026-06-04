<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MultiBranchPhase2 extends Migration
{
    /** @var list<string> */
    private array $branchScopedTables = [
        'visits',
        'incentive_rules',
        'whatsapp_campaigns',
        'driver_monthly_summaries',
        'driver_bonus_awards',
        'audit_logs',
    ];

    public function up()
    {
        $defaultBranchId = (int) ($this->db->table('branches')->selectMin('id')->get()->getRow()->id ?? 0);
        if ($defaultBranchId <= 0) {
            return;
        }

        foreach ($this->branchScopedTables as $table) {
            if (!$this->db->tableExists($table) || $this->db->fieldExists('branch_id', $table)) {
                continue;
            }

            $this->forge->addColumn($table, [
                'branch_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);

            $this->db->query("UPDATE `{$table}` SET branch_id = ? WHERE branch_id IS NULL", [$defaultBranchId]);
            $this->db->query("ALTER TABLE `{$table}` MODIFY branch_id INT(11) UNSIGNED NOT NULL");
            $this->db->query("ALTER TABLE `{$table}` ADD KEY `{$table}_branch_id_index` (`branch_id`)");
            $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE");
        }

        $this->updateDriverMonthlySummaryUniqueKey();
        $this->updateDriverBonusAwardsUniqueKey();
    }

    public function down()
    {
        $this->restoreDriverBonusAwardsUniqueKey();
        $this->restoreDriverMonthlySummaryUniqueKey();

        foreach (array_reverse($this->branchScopedTables) as $table) {
            if (!$this->db->tableExists($table) || !$this->db->fieldExists('branch_id', $table)) {
                continue;
            }

            $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$table}_branch_id_foreign`");
            $this->db->query("ALTER TABLE `{$table}` DROP KEY `{$table}_branch_id_index`");
            $this->forge->dropColumn($table, 'branch_id');
        }
    }

    private function updateDriverMonthlySummaryUniqueKey(): void
    {
        if (!$this->db->tableExists('driver_monthly_summaries')) {
            return;
        }

        $indexes = $this->db->getIndexData('driver_monthly_summaries');
        foreach ($indexes as $index) {
            $fields = $index->fields ?? [];
            if (count($fields) === 3 && in_array('driver_id', $fields, true) && in_array('year', $fields, true) && in_array('month', $fields, true)) {
                $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP INDEX `' . $index->name . '`');
            }
        }

        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD UNIQUE KEY `driver_monthly_summaries_branch_period_unique` (`driver_id`, `branch_id`, `year`, `month`)');
    }

    private function restoreDriverMonthlySummaryUniqueKey(): void
    {
        if (!$this->db->tableExists('driver_monthly_summaries')) {
            return;
        }

        $this->db->query('ALTER TABLE `driver_monthly_summaries` DROP INDEX `driver_monthly_summaries_branch_period_unique`');
        $this->db->query('ALTER TABLE `driver_monthly_summaries` ADD UNIQUE KEY `driver_id` (`driver_id`, `year`, `month`)');
    }

    private function updateDriverBonusAwardsUniqueKey(): void
    {
        if (!$this->db->tableExists('driver_bonus_awards')) {
            return;
        }

        $this->db->query('ALTER TABLE `driver_bonus_awards` DROP INDEX `driver_bonus_awards_unique_rule_month`');
        $this->db->query('ALTER TABLE `driver_bonus_awards` ADD UNIQUE KEY `driver_bonus_awards_branch_rule_month` (`driver_id`, `branch_id`, `incentive_rule_id`, `year`, `month`)');
    }

    private function restoreDriverBonusAwardsUniqueKey(): void
    {
        if (!$this->db->tableExists('driver_bonus_awards')) {
            return;
        }

        $this->db->query('ALTER TABLE `driver_bonus_awards` DROP INDEX `driver_bonus_awards_branch_rule_month`');
        $this->db->query('ALTER TABLE `driver_bonus_awards` ADD UNIQUE KEY `driver_bonus_awards_unique_rule_month` (`driver_id`, `incentive_rule_id`, `year`, `month`)');
    }
}

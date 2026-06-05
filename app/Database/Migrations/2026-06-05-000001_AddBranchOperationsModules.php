<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBranchOperationsModules extends Migration
{
    public function up()
    {
        $this->createExpensesTable();
        $this->createPayoutsTable();
        $this->createVehicleBranchActivityTable();
        $this->createDriverLoyaltyAccountsTable();
    }

    public function down()
    {
        $this->forge->dropTable('driver_loyalty_accounts', true);
        $this->forge->dropTable('vehicle_branch_activity', true);
        $this->forge->dropTable('payouts', true);
        $this->forge->dropTable('expenses', true);
    }

    private function createExpensesTable(): void
    {
        if ($this->db->tableExists('expenses')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category' => ['type' => 'VARCHAR', 'constraint' => 100],
            'vendor_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'expense_date' => ['type' => 'DATE'],
            'payment_mode' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'submitted', 'approved', 'rejected', 'paid'], 'default' => 'submitted'],
            'created_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['branch_id', 'expense_date']);
        $this->forge->addKey('created_by_user_id');
        $this->forge->addKey('approved_by_user_id');
        $this->forge->createTable('expenses', true);

        $this->db->query('ALTER TABLE `expenses` ADD CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `expenses` ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `expenses` ADD CONSTRAINT `expenses_approved_by_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    private function createPayoutsTable(): void
    {
        if ($this->db->tableExists('payouts')) {
            $this->ensurePayoutsConstraints();
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'driver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'visit_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'expense_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'payout_type' => ['type' => 'ENUM', 'constraint' => ['driver_incentive', 'driver_bonus', 'expense_reimbursement', 'vendor_payment', 'other'], 'default' => 'other'],
            'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'payout_date' => ['type' => 'DATE'],
            'payment_mode' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'paid', 'cancelled'], 'default' => 'pending'],
            'created_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'paid_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['branch_id', 'payout_date']);
        $this->forge->addKey('driver_id');
        $this->forge->addKey('visit_id');
        $this->forge->addKey('expense_id');
        $this->forge->createTable('payouts', true);

        $this->db->query('ALTER TABLE `payouts` ADD CONSTRAINT `payouts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `payouts` ADD CONSTRAINT `payouts_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->ensurePayoutsConstraints();
    }

    private function createVehicleBranchActivityTable(): void
    {
        if ($this->db->tableExists('vehicle_branch_activity')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'branch_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'vehicle_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'driver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'visit_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'activity_type' => ['type' => 'ENUM', 'constraint' => ['visit', 'manual_check', 'assignment_review'], 'default' => 'visit'],
            'activity_at' => ['type' => 'DATETIME'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_by_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['branch_id', 'activity_at']);
        $this->forge->addKey('vehicle_id');
        $this->forge->addKey('driver_id');
        $this->forge->createTable('vehicle_branch_activity', true);

        $this->db->query('ALTER TABLE `vehicle_branch_activity` ADD CONSTRAINT `vehicle_branch_activity_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `vehicle_branch_activity` ADD CONSTRAINT `vehicle_branch_activity_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `vehicle_branch_activity` ADD CONSTRAINT `vehicle_branch_activity_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `vehicle_branch_activity` ADD CONSTRAINT `vehicle_branch_activity_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    private function ensurePayoutsConstraints(): void
    {
        if (!$this->db->tableExists('payouts')) {
            return;
        }

        $this->db->query('ALTER TABLE `payouts` MODIFY `visit_id` BIGINT(20) UNSIGNED NULL');

        if (!$this->constraintExists('payouts', 'payouts_visit_id_foreign')) {
            $this->db->query('ALTER TABLE `payouts` ADD CONSTRAINT `payouts_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        if (!$this->constraintExists('payouts', 'payouts_expense_id_foreign')) {
            $this->db->query('ALTER TABLE `payouts` ADD CONSTRAINT `payouts_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    private function constraintExists(string $table, string $constraint): bool
    {
        $row = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? LIMIT 1',
            [$table, $constraint]
        )->getRowArray();

        return $row !== null;
    }

    private function createDriverLoyaltyAccountsTable(): void
    {
        if ($this->db->tableExists('driver_loyalty_accounts')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'driver_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_visits' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_branches_visited' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_guests' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_cash_incentive' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'total_bonus_paid' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => '0.00'],
            'loyalty_points' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'tier' => ['type' => 'ENUM', 'constraint' => ['bronze', 'silver', 'gold', 'platinum'], 'default' => 'bronze'],
            'last_visit_at' => ['type' => 'DATETIME', 'null' => true],
            'computed_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('driver_id', 'driver_loyalty_accounts_driver_unique');
        $this->forge->createTable('driver_loyalty_accounts', true);

        $this->db->query('ALTER TABLE `driver_loyalty_accounts` ADD CONSTRAINT `driver_loyalty_accounts_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}

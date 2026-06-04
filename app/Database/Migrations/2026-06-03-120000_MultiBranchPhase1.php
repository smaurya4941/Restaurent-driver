<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MultiBranchPhase1 extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('branches')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'branch_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'unique'     => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'state' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default'    => 'active',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('branches', true);
        }

        if (!$this->db->fieldExists('branch_id', 'users')) {
            $this->forge->addColumn('users', [
                'branch_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'role_id',
                ],
            ]);
            $this->db->query('ALTER TABLE `users` ADD KEY `users_branch_id_index` (`branch_id`)');
            $this->db->query('ALTER TABLE `users` ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        $this->seedRoles();
        $this->seedDefaultBranches();
        $this->backfillUserBranches();
    }

    public function down()
    {
        if ($this->db->fieldExists('branch_id', 'users')) {
            $this->db->query('ALTER TABLE `users` DROP FOREIGN KEY `users_branch_id_foreign`');
            $this->db->query('ALTER TABLE `users` DROP KEY `users_branch_id_index`');
            $this->forge->dropColumn('users', 'branch_id');
        }
    }

    private function seedRoles(): void
    {
        $legacyMap = [
            10 => 5,
            11 => 2,
            12 => 4,
            13 => 3,
        ];

        foreach ($legacyMap as $legacyId => $newId) {
            $this->db->query('UPDATE users SET role_id = ? WHERE role_id = ?', [$newId, $legacyId]);
        }

        $this->db->table('roles')->whereNotIn('id', [1, 2, 3, 4, 5])->delete();

        $roles = [
            1 => ['name' => 'branch_admin', 'description' => 'Manages drivers, visits, reports, and employees for an assigned branch.'],
            2 => ['name' => 'accountant', 'description' => 'Access to branch reports and financial summaries.'],
            3 => ['name' => 'security', 'description' => 'Driver verification, registrations, and visit entry for a branch.'],
            4 => ['name' => 'staff', 'description' => 'General branch staff with operational access.'],
            5 => ['name' => 'super_admin', 'description' => 'Full access across all branches and national data.'],
        ];

        foreach ($roles as $id => $role) {
            $existing = $this->db->table('roles')->where('id', $id)->get()->getRowArray();
            $payload = $role + [
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->db->table('roles')->where('id', $id)->update($payload);
                continue;
            }

            $payload['id'] = $id;
            $payload['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('roles')->insert($payload);
        }
    }

    private function seedDefaultBranches(): void
    {
        $branches = [
            [
                'name'        => 'Hawa Hawai Ghaziabad',
                'branch_code' => 'HH-GZB',
                'city'        => 'Ghaziabad',
                'state'       => 'Uttar Pradesh',
                'address'     => 'Ghaziabad',
                'phone'       => null,
                'status'      => 'active',
            ],
            [
                'name'        => 'Hawa Hawai Mumbai',
                'branch_code' => 'HH-MUM',
                'city'        => 'Mumbai',
                'state'       => 'Maharashtra',
                'address'     => 'Mumbai',
                'phone'       => null,
                'status'      => 'active',
            ],
            [
                'name'        => 'Hawa Hawai Haridwar',
                'branch_code' => 'HH-HRW',
                'city'        => 'Haridwar',
                'state'       => 'Uttarakhand',
                'address'     => 'Haridwar',
                'phone'       => null,
                'status'      => 'active',
            ],
        ];

        foreach ($branches as $branch) {
            $existing = $this->db->table('branches')->where('branch_code', $branch['branch_code'])->get()->getRowArray();
            $payload = $branch + ['updated_at' => date('Y-m-d H:i:s')];

            if ($existing) {
                $this->db->table('branches')->where('id', $existing['id'])->update($payload);
                continue;
            }

            $payload['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('branches')->insert($payload);
        }
    }

    private function backfillUserBranches(): void
    {
        $defaultBranchId = (int) ($this->db->table('branches')->selectMin('id')->get()->getRow()->id ?? 0);
        if ($defaultBranchId <= 0) {
            return;
        }

        $this->db->query(
            'UPDATE users SET branch_id = ? WHERE branch_id IS NULL AND role_id <> ?',
            [$defaultBranchId, 5]
        );

        $this->db->query(
            "UPDATE users SET role_id = 5, branch_id = NULL WHERE email = 'admin@hawahawai.com'"
        );

        $this->db->query(
            "UPDATE users SET role_id = 1 WHERE role_id = 1 AND email <> 'admin@hawahawai.com' AND name = 'System Admin'"
        );
    }
}

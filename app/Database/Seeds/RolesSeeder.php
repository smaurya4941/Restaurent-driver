<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'          => 1,
                'name'        => 'branch_admin',
                'description' => 'Manages drivers, visits, reports, and employees for an assigned branch.',
            ],
            [
                'id'          => 2,
                'name'        => 'accountant',
                'description' => 'Access to branch reports and financial summaries.',
            ],
            [
                'id'          => 3,
                'name'        => 'security',
                'description' => 'Driver verification, registrations, and visit entry for a branch.',
            ],
            [
                'id'          => 4,
                'name'        => 'staff',
                'description' => 'General branch staff with operational access.',
            ],
            [
                'id'          => 5,
                'name'        => 'super_admin',
                'description' => 'Full access across all branches and national data.',
            ],
        ];

        foreach ($roles as $role) {
            $existing = $this->db->table('roles')->where('id', $role['id'])->get()->getRowArray();

            if ($existing) {
                $this->db->table('roles')->where('id', $role['id'])->update($role);
                continue;
            }

            $this->db->table('roles')->insert($role);
        }
    }
}

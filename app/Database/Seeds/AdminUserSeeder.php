<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $email = 'admin@hawahawai.com';
        $defaultBranchId = (int) ($this->db->table('branches')->selectMin('id')->get()->getRow()->id ?? 0);

        $adminUser = [
            'name'      => 'Branch Admin',
            'email'     => $email,
            'password'  => password_hash('123456', PASSWORD_BCRYPT),
            'role_id'   => 1,
            'branch_id' => $defaultBranchId > 0 ? $defaultBranchId : null,
            'status'    => 'active',
        ];

        $existing = $this->db->table('users')->where('email', $email)->get()->getRowArray();

        if ($existing) {
            $this->db->table('users')->where('email', $email)->update($adminUser);
            return;
        }

        $this->db->table('users')->insert($adminUser);
    }
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $email = 'superadmin@hawahawai.com';

        $payload = [
            'name'      => 'Super Admin',
            'email'     => $email,
            'password'  => password_hash('123456', PASSWORD_BCRYPT),
            'role_id'   => 5,
            'branch_id' => null,
            'status'    => 'active',
        ];

        $existing = $this->db->table('users')->where('email', $email)->get()->getRowArray();
        if ($existing) {
            $this->db->table('users')->where('email', $email)->update($payload);
            return;
        }

        $this->db->table('users')->insert($payload);
    }
}

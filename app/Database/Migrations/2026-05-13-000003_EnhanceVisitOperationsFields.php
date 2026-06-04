<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceVisitOperationsFields extends Migration
{
    public function up()
    {
        $fields = [
            'verification_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'verification_method',
            ],
            'food_quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'food_offered',
            ],
            'food_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'food_quantity',
            ],
            'handled_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'verified_by_user_id',
            ],
            'incentive_given_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'handled_by_user_id',
            ],
        ];

        $this->forge->addColumn('visits', $fields);
        $this->db->query('ALTER TABLE `visits` ADD KEY `visits_handled_by_user_id_index` (`handled_by_user_id`)');
        $this->db->query('ALTER TABLE `visits` ADD KEY `visits_incentive_given_by_user_id_index` (`incentive_given_by_user_id`)');
        $this->db->query('ALTER TABLE `visits` ADD CONSTRAINT `visits_handled_by_user_id_foreign` FOREIGN KEY (`handled_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `visits` ADD CONSTRAINT `visits_incentive_given_by_user_id_foreign` FOREIGN KEY (`incentive_given_by_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `visits` DROP FOREIGN KEY `visits_incentive_given_by_user_id_foreign`');
        $this->db->query('ALTER TABLE `visits` DROP FOREIGN KEY `visits_handled_by_user_id_foreign`');
        $this->db->query('ALTER TABLE `visits` DROP KEY `visits_incentive_given_by_user_id_index`');
        $this->db->query('ALTER TABLE `visits` DROP KEY `visits_handled_by_user_id_index`');

        $this->forge->dropColumn('visits', [
            'verification_reference',
            'food_quantity',
            'food_type',
            'handled_by_user_id',
            'incentive_given_by_user_id',
        ]);
    }
}

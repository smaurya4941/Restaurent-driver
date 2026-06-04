<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        //Fields: id name branch_code city state address phone status created_at updated_at
        $this->forge->addField(
            [
                'id'=>[
                    'type'=>'INT',
                    'constraint'=>11,
                    'unsigned'=>true,
                    'auto_increment'=>true
                ],
                'name'=>[
                    'type'=>'VARCHAR',
                    'constraint'=>255
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
            ]
        );

        $this->forge->addKey('id', true);

        $this->forge->createTable('branches');
    }

    public function down()
    {
        //
        $this->forge->dropTable('branches');
    }
}

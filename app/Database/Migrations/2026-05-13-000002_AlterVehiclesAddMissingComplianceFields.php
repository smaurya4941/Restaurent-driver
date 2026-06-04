<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterVehiclesAddMissingComplianceFields extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('rc_number', 'vehicles')) {
            $fields['rc_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'vehicle_type',
            ];
        }

        if (! $this->db->fieldExists('rc_expiry_date', 'vehicles')) {
            $fields['rc_expiry_date'] = [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'rc_number',
            ];
        }

        if (! $this->db->fieldExists('permit_number', 'vehicles')) {
            $fields['permit_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'rc_expiry_date',
            ];
        }

        if (! $this->db->fieldExists('permit_expiry_date', 'vehicles')) {
            $fields['permit_expiry_date'] = [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'permit_number',
            ];
        }

        if (! $this->db->fieldExists('insurance_policy_number', 'vehicles')) {
            $fields['insurance_policy_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'permit_expiry_date',
            ];
        }

        if (! $this->db->fieldExists('insurance_expiry_date', 'vehicles')) {
            $fields['insurance_expiry_date'] = [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'insurance_policy_number',
            ];
        }

        if (! $this->db->fieldExists('assigned_from', 'vehicles')) {
            $fields['assigned_from'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'status',
            ];
        }

        if (! $this->db->fieldExists('assigned_until', 'vehicles')) {
            $fields['assigned_until'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'assigned_from',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('vehicles', $fields);
        }
    }

    public function down()
    {
        $columns = [
            'assigned_until',
            'assigned_from',
            'insurance_expiry_date',
            'insurance_policy_number',
            'permit_expiry_date',
            'permit_number',
            'rc_expiry_date',
            'rc_number',
        ];

        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'vehicles')) {
                $this->forge->dropColumn('vehicles', $column);
            }
        }
    }
}

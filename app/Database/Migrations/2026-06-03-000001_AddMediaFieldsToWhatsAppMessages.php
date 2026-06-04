<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMediaFieldsToWhatsAppMessages extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('media_type', 'whatsapp_messages')) {
            $fields['media_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'message_body',
            ];
        }

        if (!$this->db->fieldExists('media_url', 'whatsapp_messages')) {
            $fields['media_url'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'media_type',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('whatsapp_messages', $fields);
        }
    }

    public function down()
    {
        foreach (['media_url', 'media_type'] as $field) {
            if ($this->db->fieldExists($field, 'whatsapp_messages')) {
                $this->forge->dropColumn('whatsapp_messages', $field);
            }
        }
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWhatsAppMarketingSystem extends Migration
{
    public function up()
    {
        $this->addConsentFieldsToDrivers();
        $this->enhanceMessageTemplates();
        $this->createWhatsAppCampaignsTable();
        $this->enhanceWhatsAppMessages();
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_campaigns', true);

        $driverFields = [
            'whatsapp_opt_in',
            'whatsapp_opt_in_at',
            'whatsapp_opt_out_at',
            'whatsapp_opt_out_reason',
        ];

        foreach ($driverFields as $field) {
            if ($this->db->fieldExists($field, 'drivers')) {
                $this->forge->dropColumn('drivers', $field);
            }
        }

        $templateFields = [
            'channel',
            'category',
            'variables_json',
        ];

        foreach ($templateFields as $field) {
            if ($this->db->fieldExists($field, 'message_templates')) {
                $this->forge->dropColumn('message_templates', $field);
            }
        }

        $messageFields = [
            'campaign_id',
            'recipient_number',
            'queue_status',
            'delivery_status',
            'attempt_count',
            'max_attempts',
            'queued_at',
            'processing_started_at',
            'delivered_at',
            'next_retry_at',
            'provider_status',
            'provider_response',
        ];

        foreach ($messageFields as $field) {
            if ($this->db->fieldExists($field, 'whatsapp_messages')) {
                $this->forge->dropColumn('whatsapp_messages', $field);
            }
        }
    }

    private function addConsentFieldsToDrivers(): void
    {
        $fields = [];

        if (!$this->db->fieldExists('whatsapp_opt_in', 'drivers')) {
            $fields['whatsapp_opt_in'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'whatsapp_number',
            ];
        }

        if (!$this->db->fieldExists('whatsapp_opt_in_at', 'drivers')) {
            $fields['whatsapp_opt_in_at'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'whatsapp_opt_in',
            ];
        }

        if (!$this->db->fieldExists('whatsapp_opt_out_at', 'drivers')) {
            $fields['whatsapp_opt_out_at'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'whatsapp_opt_in_at',
            ];
        }

        if (!$this->db->fieldExists('whatsapp_opt_out_reason', 'drivers')) {
            $fields['whatsapp_opt_out_reason'] = [
                'type'  => 'VARCHAR',
                'constraint' => 255,
                'null'  => true,
                'after' => 'whatsapp_opt_out_at',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('drivers', $fields);
            $this->db->query("UPDATE drivers SET whatsapp_opt_in = 1, whatsapp_opt_in_at = COALESCE(created_at, NOW()) WHERE whatsapp_number IS NOT NULL AND whatsapp_number <> ''");
            $this->db->query("UPDATE drivers SET whatsapp_opt_in = 0 WHERE whatsapp_number IS NULL OR whatsapp_number = ''");
        }
    }

    private function enhanceMessageTemplates(): void
    {
        $fields = [];

        if (!$this->db->fieldExists('channel', 'message_templates')) {
            $fields['channel'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'whatsapp',
                'after'      => 'type',
            ];
        }

        if (!$this->db->fieldExists('category', 'message_templates')) {
            $fields['category'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'custom',
                'after'      => 'channel',
            ];
        }

        if (!$this->db->fieldExists('variables_json', 'message_templates')) {
            $fields['variables_json'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'content',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('message_templates', $fields);
        }

        $hasTemplates = $this->db->table('message_templates')->countAllResults();
        if ($hasTemplates === 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('message_templates')->insertBatch([
                [
                    'name' => 'Daily Driver Offer',
                    'type' => 'daily_offer',
                    'channel' => 'whatsapp',
                    'category' => 'daily_offer',
                    'content' => 'Hello {{driver_name}}, today\'s special offer is live. Visit us today for driver benefits and support.',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Festival Greetings Offer',
                    'type' => 'festival_offer',
                    'channel' => 'whatsapp',
                    'category' => 'festival_offer',
                    'content' => 'Happy celebrations {{driver_name}}. We have a special festival offer waiting for you today.',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    private function createWhatsAppCampaignsTable(): void
    {
        if ($this->db->tableExists('whatsapp_campaigns')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'campaign_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'comment'    => 'daily_offer, festival_offer, custom',
            ],
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'message_body' => [
                'type' => 'TEXT',
            ],
            'audience_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'all_opted_in_drivers',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'draft',
                'comment'    => 'draft, scheduled, processing, completed, paused, cancelled',
            ],
            'schedule_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'one_time',
                'comment'    => 'one_time, daily',
            ],
            'scheduled_for' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_dispatched_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'total_recipients' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'sent_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'delivered_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'failed_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('campaign_type');
        $this->forge->addKey('status');
        $this->forge->addKey('schedule_type');
        $this->forge->addKey('scheduled_for');
        $this->forge->addForeignKey('template_id', 'message_templates', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('whatsapp_campaigns', true);
    }

    private function enhanceWhatsAppMessages(): void
    {
        $fields = [];

        if (!$this->db->fieldExists('campaign_id', 'whatsapp_messages')) {
            $fields['campaign_id'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ];
        }

        if (!$this->db->fieldExists('recipient_number', 'whatsapp_messages')) {
            $fields['recipient_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'driver_id',
            ];
        }

        if (!$this->db->fieldExists('queue_status', 'whatsapp_messages')) {
            $fields['queue_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'queued',
                'after'      => 'status',
            ];
        }

        if (!$this->db->fieldExists('delivery_status', 'whatsapp_messages')) {
            $fields['delivery_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'after'      => 'queue_status',
            ];
        }

        if (!$this->db->fieldExists('attempt_count', 'whatsapp_messages')) {
            $fields['attempt_count'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'delivery_status',
            ];
        }

        if (!$this->db->fieldExists('max_attempts', 'whatsapp_messages')) {
            $fields['max_attempts'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 3,
                'after'      => 'attempt_count',
            ];
        }

        if (!$this->db->fieldExists('queued_at', 'whatsapp_messages')) {
            $fields['queued_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'max_attempts',
            ];
        }

        if (!$this->db->fieldExists('processing_started_at', 'whatsapp_messages')) {
            $fields['processing_started_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'queued_at',
            ];
        }

        if (!$this->db->fieldExists('delivered_at', 'whatsapp_messages')) {
            $fields['delivered_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'sent_at',
            ];
        }

        if (!$this->db->fieldExists('next_retry_at', 'whatsapp_messages')) {
            $fields['next_retry_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'failed_at',
            ];
        }

        if (!$this->db->fieldExists('provider_status', 'whatsapp_messages')) {
            $fields['provider_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'provider_message_id',
            ];
        }

        if (!$this->db->fieldExists('provider_response', 'whatsapp_messages')) {
            $fields['provider_response'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'provider_status',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('whatsapp_messages', $fields);
        }

        if ($this->db->fieldExists('campaign_id', 'whatsapp_messages')) {
            $this->db->query('ALTER TABLE whatsapp_messages ADD INDEX idx_whatsapp_messages_campaign_id (campaign_id)');
            $this->db->query('ALTER TABLE whatsapp_messages ADD CONSTRAINT fk_whatsapp_messages_campaign_id FOREIGN KEY (campaign_id) REFERENCES whatsapp_campaigns(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        $this->db->query("UPDATE whatsapp_messages SET recipient_number = (SELECT drivers.whatsapp_number FROM drivers WHERE drivers.id = whatsapp_messages.driver_id) WHERE recipient_number IS NULL");
        $this->db->query("UPDATE whatsapp_messages SET queue_status = status WHERE queue_status IS NULL OR queue_status = ''");
        $this->db->query("UPDATE whatsapp_messages SET delivery_status = CASE WHEN status = 'delivered' THEN 'delivered' WHEN status = 'failed' THEN 'failed' WHEN status = 'sent' THEN 'sent' ELSE 'pending' END WHERE delivery_status IS NULL OR delivery_status = ''");
        $this->db->query("UPDATE whatsapp_messages SET queued_at = COALESCE(created_at, NOW()) WHERE queued_at IS NULL");
    }
}

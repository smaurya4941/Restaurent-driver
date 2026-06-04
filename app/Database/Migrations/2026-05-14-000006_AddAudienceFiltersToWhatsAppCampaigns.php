<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAudienceFiltersToWhatsAppCampaigns extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('filters_json', 'whatsapp_campaigns')) {
            $this->forge->addColumn('whatsapp_campaigns', [
                'filters_json' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'audience_type',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('filters_json', 'whatsapp_campaigns')) {
            $this->forge->dropColumn('whatsapp_campaigns', 'filters_json');
        }
    }
}

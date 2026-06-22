<?php

namespace App\Controllers;

use App\Models\MessageTemplateModel;
use App\Services\DriverWhatsAppService;
use App\Services\WhatsAppQueueService;

class WhatsAppController extends BaseController
{

    public function index()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $filters = $this->collectFilters();
        $templateModel = new MessageTemplateModel();
        $templates = $templateModel->where('is_active', 1)->findAll();

        return view('whatsappCampaigns', [
            'currentFilters' => $filters,
            'groupRows' => (new DriverWhatsAppService())->buildDriverGroupReport($filters),
            'templates' => $templates,
        ]);
    }

    public function sendGroupedMessage()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $validationRules = [
            'message_body' => 'required|max_length[5000]',
            'media_url' => 'permit_empty|valid_url_strict|max_length[2048]',
            'message_image' => 'permit_empty|max_size[message_image,5120]|is_image[message_image]|mime_in[message_image,image/jpg,image/jpeg,image/png,image/webp]',
            'send_scope' => 'permit_empty|in_list[group,selected]',
            'vehicle_type' => 'permit_empty|max_length[50]',
            'min_visits' => 'permit_empty|is_natural',
            'max_visits' => 'permit_empty|is_natural',
            'min_guests' => 'permit_empty|is_natural',
            'max_guests' => 'permit_empty|is_natural',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $filters = $this->collectFilters();
        $service = new DriverWhatsAppService();
        $sendScope = (string) ($this->request->getPost('send_scope') ?? 'selected');
        $selectedDriverIds = $this->collectSelectedDriverIds();
        $mediaUrl = $this->storeCampaignImage() ?? trim((string) $this->request->getPost('media_url'));

        if (!$service->validateFilterRange($filters)) {
            return redirect()->back()->withInput()->with('error', 'Please check the filter ranges.');
        }

        if ($sendScope === 'selected') {
            if ($selectedDriverIds === []) {
                return redirect()->back()->withInput()->with('error', 'Please select at least one driver.');
            }

            $queued = $service->queueSelectedMessage(
                $filters,
                $selectedDriverIds,
                (string) $this->request->getPost('message_body'),
                (int) (session()->get('user')['id'] ?? 0),
                $mediaUrl !== '' ? $mediaUrl : null
            );
        } else {
            $queued = $service->queueGroupedMessage(
                $filters,
                (string) $this->request->getPost('message_body'),
                (int) (session()->get('user')['id'] ?? 0),
                $mediaUrl !== '' ? $mediaUrl : null
            );
        }

        if ($queued === 0) {
            return redirect()->back()->withInput()->with('error', 'No matching selected drivers with WhatsApp numbers were found.');
        }

        $result = (new WhatsAppQueueService())->processQueuedMessages();
        $this->logAudit('whatsapp.campaign.sent', 'whatsapp_campaign', $queued, null, [
            'filters' => $filters,
            'scope' => $sendScope,
            'selected_driver_ids' => $selectedDriverIds,
            'media_url' => $mediaUrl !== '' ? $mediaUrl : null,
            'queued' => $queued,
            'sent' => $result['sent'],
            'failed' => $result['failed'],
        ]);

        return redirect()->to('/whatsapp-campaigns')->with(
            'success',
            'Successfully queued ' . $queued . ' new message(s). Worker processed ' . $result['processed'] . ' message(s) from the queue (Sent: ' . $result['sent'] . ', Failed: ' . $result['failed'] . ').'
        );
    }

    private function collectFilters(): array
    {
        return [
            'vehicle_type' => trim((string) $this->request->getGetPost('vehicle_type')),
            'min_visits' => trim((string) $this->request->getGetPost('min_visits')),
            'max_visits' => trim((string) $this->request->getGetPost('max_visits')),
            'min_guests' => trim((string) $this->request->getGetPost('min_guests')),
            'max_guests' => trim((string) $this->request->getGetPost('max_guests')),
        ];
    }

    private function collectSelectedDriverIds(): array
    {
        $selectedDriverIds = $this->request->getPost('selected_driver_ids') ?? [];

        if (!is_array($selectedDriverIds)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('intval', $selectedDriverIds),
            static fn ($id) => $id > 0
        )));
    }

    private function storeCampaignImage(): ?string
    {
        $file = $this->request->getFile('message_image');

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!$file->isValid()) {
            return null;
        }

        $relativeDirectory = 'uploads/whatsapp-campaigns';
        $targetDirectory = FCPATH . $relativeDirectory;

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDirectory, $newName, true);

        return base_url($relativeDirectory . '/' . $newName);
    }
}

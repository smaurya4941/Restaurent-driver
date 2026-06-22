<?php

namespace App\Controllers;

use App\Models\MessageTemplateModel;

class MessageTemplateController extends BaseController
{
    private MessageTemplateModel $templateModel;

    public function __construct()
    {
        $this->templateModel = new MessageTemplateModel();
    }

    public function index()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $templates = $this->templateModel->findAll();

        return view('messageTemplates', [
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        return view('messageTemplateForm', [
            'template' => null,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $rules = [
            'name'    => 'required|max_length[150]',
            'content' => 'required|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->templateModel->insert([
            'name'      => $this->request->getPost('name'),
            'type'      => 'custom',
            'channel'   => 'whatsapp',
            'category'  => 'custom',
            'content'   => $this->request->getPost('content'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/message-templates')->with('success', 'Template created successfully.');
    }

    public function edit($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $template = $this->templateModel->find($id);

        if (!$template) {
            return redirect()->to('/message-templates')->with('error', 'Template not found.');
        }

        return view('messageTemplateForm', [
            'template' => $template,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $template = $this->templateModel->find($id);

        if (!$template) {
            return redirect()->to('/message-templates')->with('error', 'Template not found.');
        }

        $rules = [
            'name'    => 'required|max_length[150]',
            'content' => 'required|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->templateModel->update($id, [
            'name'      => $this->request->getPost('name'),
            'content'   => $this->request->getPost('content'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/message-templates')->with('success', 'Template updated successfully.');
    }

    public function delete($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $template = $this->templateModel->find($id);

        if ($template) {
            $this->templateModel->delete($id);
            return redirect()->to('/message-templates')->with('success', 'Template deleted successfully.');
        }

        return redirect()->to('/message-templates')->with('error', 'Template not found.');
    }

    public function toggle($id)
    {
        if ($redirect = $this->authorize($this->branchManagementRoles())) {
            return $redirect;
        }

        $template = $this->templateModel->find($id);

        if ($template) {
            $this->templateModel->update($id, [
                'is_active' => $template['is_active'] ? 0 : 1,
            ]);
            return redirect()->to('/message-templates')->with('success', 'Template status toggled.');
        }

        return redirect()->to('/message-templates')->with('error', 'Template not found.');
    }
}

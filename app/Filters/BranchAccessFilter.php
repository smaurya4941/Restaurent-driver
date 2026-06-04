<?php

namespace App\Filters;

use App\Services\BranchContext;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class BranchAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return null;
        }

        $context = new BranchContext();
        if ($context->isSuperAdmin()) {
            return null;
        }

        if ($context->getUserBranchId() !== null) {
            return null;
        }

        session()->destroy();

        return redirect()
            ->to(base_url())
            ->with('failed', 'Your account is not assigned to a branch. Please contact a super admin.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}

<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Services\BranchContext;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected const ROLE_BRANCH_ADMIN = 1;
    /** @deprecated Use ROLE_BRANCH_ADMIN */
    protected const ROLE_ADMIN = self::ROLE_BRANCH_ADMIN;
    protected const ROLE_ACCOUNTANT = 2;
    protected const ROLE_SECURITY = 3;
    protected const ROLE_STAFF = 4;
    protected const ROLE_SUPER_ADMIN = 5;
    protected const ROLE_FRONTDESK = self::ROLE_SECURITY;

    protected const USER_STATUS_ACTIVE = 'active';
    protected const USER_STATUS_INACTIVE = 'inactive';
    protected const USER_STATUS_DISABLED = 'disabled';

    protected BranchContext $branchContext;

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->branchContext = new BranchContext();
    }

    protected function requireLogin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url())->with('failed', 'Please sign in to continue.');
        }

        $user = session()->get('user');
        $status = strtolower((string) ($user['status'] ?? ''));

        if ($status !== '' && $status !== self::USER_STATUS_ACTIVE) {
            session()->destroy();
            return redirect()->to(base_url())->with('failed', 'Your account is not active. Please contact an administrator.');
        }

        return null;
    }

    protected function authorize(array $allowedRoles)
    {
        $loginRedirect = $this->requireLogin();
        if ($loginRedirect !== null) {
            return $loginRedirect;
        }

        $role = (int) session()->get('role');
        if (!in_array($role, $allowedRoles, true)) {
            return redirect()->to('/dashboard')->with('error', 'You are not authorized to access that page.');
        }

        return null;
    }

    protected function authorizeSuperAdmin()
    {
        return $this->authorize([self::ROLE_SUPER_ADMIN]);
    }

    protected function adminLikeRoles(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_BRANCH_ADMIN, self::ROLE_SECURITY, self::ROLE_STAFF];
    }

    protected function branchManagementRoles(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_BRANCH_ADMIN];
    }

    protected function visitEntryRoles(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_BRANCH_ADMIN, self::ROLE_SECURITY, self::ROLE_STAFF];
    }

    protected function reportingRoles(): array
    {
        return [self::ROLE_SUPER_ADMIN, self::ROLE_BRANCH_ADMIN, self::ROLE_SECURITY, self::ROLE_STAFF, self::ROLE_ACCOUNTANT];
    }

    protected function isSuperAdmin(): bool
    {
        return $this->branchContext->isSuperAdmin();
    }

    protected function requireBranchId(): int
    {
        return $this->branchContext->requireBranchId();
    }

    protected function applyBranchScope(Model|BaseBuilder $target, string $column = 'branch_id'): Model|BaseBuilder
    {
        return $this->branchContext->applyScope($target, $column);
    }

    protected function currentUser(): array
    {
        return (array) (session()->get('user') ?? []);
    }

    protected function logAudit(string $action, string $entityType, int $entityId, ?array $oldValues = null, ?array $newValues = null): void
    {
        $payload = [
            'user_id' => $this->currentUser()['id'] ?? null,
            'branch_id' => $this->requireBranchId(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => (string) $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        (new AuditLogModel())->insert($payload);
    }
}

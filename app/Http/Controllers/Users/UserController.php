<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('role_id'), fn ($query) => $query->where('role_id', $request->query('role_id')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::query()->create($request->validated());
        $this->auditService->logModel(AuditLog::ACTION_CREATE, $user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load('role.permissions');

        return view('users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($user->id === $request->user()->id && ($data['status'] ?? null) === User::STATUS_INACTIVE) {
            return back()
                ->withInput()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $old = $this->auditService->modelSnapshot($user);
        $user->update($data);
        $this->auditService->logModel(AuditLog::ACTION_UPDATE, $user, $old);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('Super Admin') && User::query()->whereHas('role', fn ($q) => $q->where('name', 'Super Admin'))->count() <= 1) {
            return back()->with('error', 'At least one Super Admin account must remain.');
        }

        $old = $this->auditService->modelSnapshot($user);
        $userId = $user->id;
        $user->delete();
        $this->auditService->log(
            action: AuditLog::ACTION_DELETE,
            tableName: 'users',
            recordId: $userId,
            oldValues: $old
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->auditService->log(
            action: AuditLog::ACTION_LOGIN,
            tableName: 'users',
            recordId: Auth::id(),
            newValues: ['email' => Auth::user()?->email]
        );

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $email = Auth::user()?->email;

        $this->auditService->log(
            action: AuditLog::ACTION_LOGOUT,
            tableName: 'users',
            recordId: $userId,
            oldValues: ['email' => $email],
            userId: $userId
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

<?php

namespace App\Http\Controllers\Backups;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $backupService,
        private readonly AuditService $auditService
    ) {}

    public function index(): View
    {
        return view('backups.index', [
            'backups' => $this->backupService->list(),
            'backupService' => $this->backupService,
        ]);
    }

    public function store(): RedirectResponse
    {
        try {
            $filename = $this->backupService->create();
            $this->auditService->log(
                action: AuditLog::ACTION_BACKUP,
                tableName: 'backups',
                newValues: ['filename' => $filename]
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to create backup. '.$exception->getMessage());
        }

        return redirect()
            ->route('backups.index')
            ->with('success', "Backup created: {$filename}");
    }

    public function download(string $backup): BinaryFileResponse|RedirectResponse
    {
        try {
            $path = $this->backupService->pathFor($backup);
        } catch (Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return response()->download($path, $backup);
    }

    public function restore(string $backup): RedirectResponse
    {
        try {
            $this->backupService->restore($backup);
            Artisan::call('cache:clear');
            $this->auditService->log(
                action: AuditLog::ACTION_RESTORE_BACKUP,
                tableName: 'backups',
                newValues: ['filename' => $backup]
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to restore backup. '.$exception->getMessage());
        }

        return redirect()
            ->route('backups.index')
            ->with('success', "Database restored from {$backup}. A safety backup of the previous database was also created.");
    }

    public function destroy(string $backup): RedirectResponse
    {
        try {
            $this->backupService->delete($backup);
            $this->auditService->log(
                action: AuditLog::ACTION_DELETE,
                tableName: 'backups',
                oldValues: ['filename' => $backup]
            );
        } catch (Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('backups.index')
            ->with('success', 'Backup deleted successfully.');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class BackupService
{
    public function directory(): string
    {
        return storage_path('app/backups');
    }

    public function ensureDirectory(): void
    {
        File::ensureDirectoryExists($this->directory());
    }

    public function databasePath(): string
    {
        $path = config('database.connections.sqlite.database');

        if (! is_string($path) || $path === '' || ! File::exists($path)) {
            throw new RuntimeException('SQLite database file was not found.');
        }

        return $path;
    }

    public function create(): string
    {
        if (config('database.default') !== 'sqlite') {
            throw new RuntimeException('Backup is only supported for SQLite in V1.');
        }

        $this->ensureDirectory();

        $filename = 'backup-'.now()->format('Ymd-His').'.sqlite';
        $destination = $this->directory().DIRECTORY_SEPARATOR.$filename;
        $sqlitePath = str_replace('\\', '/', $destination);

        DB::connection()->getPdo()->exec("VACUUM INTO '{$sqlitePath}'");

        if (! File::exists($destination)) {
            throw new RuntimeException('Backup file was not created.');
        }

        return $filename;
    }

    /**
     * @return Collection<int, array{filename:string, path:string, size:int, modified_at:string}>
     */
    public function list(): Collection
    {
        $this->ensureDirectory();

        return collect(File::files($this->directory()))
            ->filter(fn ($file) => $this->isValidBackupName($file->getFilename()))
            ->map(fn ($file) => [
                'filename' => $file->getFilename(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->sortByDesc('filename')
            ->values();
    }

    public function pathFor(string $filename): string
    {
        if (! $this->isValidBackupName($filename)) {
            throw new RuntimeException('Invalid backup filename.');
        }

        $path = $this->directory().DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($path)) {
            throw new RuntimeException('Backup file not found.');
        }

        return $path;
    }

    public function restore(string $filename): void
    {
        if (config('database.default') !== 'sqlite') {
            throw new RuntimeException('Restore is only supported for SQLite in V1.');
        }

        $backupPath = $this->pathFor($filename);
        $databasePath = $this->databasePath();

        // Safety copy of current DB before overwrite.
        $this->create();

        DB::purge();
        DB::disconnect();

        if (! @copy($backupPath, $databasePath)) {
            throw new RuntimeException('Unable to restore the selected backup.');
        }
    }

    public function delete(string $filename): void
    {
        File::delete($this->pathFor($filename));
    }

    public function isValidBackupName(string $filename): bool
    {
        return (bool) preg_match('/^backup-\d{8}-\d{6}\.sqlite$/', $filename);
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }
}

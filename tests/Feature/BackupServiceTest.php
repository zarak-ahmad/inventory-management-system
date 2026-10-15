<?php

namespace Tests\Feature;

use App\Services\BackupService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\Concerns\SeedsApplicationData;
use Tests\TestCase;

class BackupServiceTest extends TestCase
{
    use SeedsApplicationData;

    public function test_sqlite_backup_can_be_created_listed_and_deleted(): void
    {
        if (config('database.default') !== 'sqlite') {
            $this->markTestSkipped('Backup tests require SQLite.');
        }

        $dir = storage_path('framework/testing');
        File::ensureDirectoryExists($dir);
        $tempDb = $dir.'/backup-service.sqlite';
        File::delete($tempDb);
        File::put($tempDb, '');

        config(['database.connections.sqlite.database' => $tempDb]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->seedApplication();

        $service = app(BackupService::class);
        $filename = $service->create();

        $this->assertTrue($service->isValidBackupName($filename));
        $this->assertFileExists($service->pathFor($filename));
        $this->assertTrue($service->list()->contains(fn ($row) => $row['filename'] === $filename));

        $service->delete($filename);
        $this->assertFileDoesNotExist($service->directory().DIRECTORY_SEPARATOR.$filename);

        File::delete($tempDb);
    }
}

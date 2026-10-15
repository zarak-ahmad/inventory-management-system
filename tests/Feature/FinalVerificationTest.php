<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Services\BackupService;
use App\Services\StockAdjustmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\SeedsApplicationData;
use Tests\TestCase;

class FinalVerificationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApplicationData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedApplication();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $this->makeAdmin();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'admin@test.local',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors();
    }

    public function test_login_and_logout_work_and_write_audit_logs(): void
    {
        $admin = $this->makeAdmin();

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_LOGIN,
            'user_id' => $admin->id,
        ]);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', [
            'action' => AuditLog::ACTION_LOGOUT,
            'user_id' => $admin->id,
        ]);
    }

    public function test_inactive_user_cannot_access_dashboard(): void
    {
        $admin = $this->makeAdmin(['status' => User::STATUS_INACTIVE]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_settings_or_users(): void
    {
        $cashier = $this->makeCashier();

        $this->actingAs($cashier)
            ->get(route('settings.edit'))
            ->assertForbidden();

        $this->actingAs($cashier)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_stock_adjustment_updates_stock(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct(['current_stock' => 10]);

        $adjustment = app(StockAdjustmentService::class)->create([
            'product_id' => $product->id,
            'adjustment_type' => StockAdjustment::TYPE_DAMAGE,
            'quantity' => 3,
            'reason' => 'Broken in transit',
        ], $admin->id);

        $product->refresh();
        $this->assertSame(7.0, (float) $product->current_stock);
        $this->assertSame(10.0, (float) $adjustment->stock_before);
        $this->assertSame(7.0, (float) $adjustment->stock_after);
    }

    public function test_password_is_hashed_when_user_created(): void
    {
        $admin = $this->makeAdmin();
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertNotSame('password', $admin->password);
    }

    public function test_backup_filename_validation_and_directory(): void
    {
        $service = app(BackupService::class);
        $service->ensureDirectory();

        $this->assertTrue($service->isValidBackupName('backup-20260730-120000.sqlite'));
        $this->assertFalse($service->isValidBackupName('../evil.sqlite'));
        $this->assertFalse($service->isValidBackupName('backup.sqlite'));
        $this->assertDirectoryExists($service->directory());
    }

    public function test_dashboard_loads_for_authenticated_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_reports_hub_requires_permission(): void
    {
        $cashier = $this->makeCashier();

        $this->actingAs($cashier)
            ->get(route('reports.index'))
            ->assertForbidden();

        $admin = $this->makeAdmin(['email' => 'reports-admin@test.local']);
        $this->actingAs($admin)
            ->get(route('reports.index'))
            ->assertOk();
    }
}

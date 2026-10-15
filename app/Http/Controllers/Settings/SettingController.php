<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\AuditLog;
use App\Services\AuditService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly AuditService $auditService
    ) {}

    public function edit(): View
    {
        return view('settings.edit', [
            'settings' => $this->settingService->all(),
            'timezones' => timezone_identifiers_list(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $old = $this->settingService->all();
        $data = $request->safe()->except(['shop_logo', 'remove_logo']);

        if ($request->hasFile('shop_logo')) {
            $data['shop_logo'] = $this->settingService->storeLogo(
                $request->file('shop_logo'),
                $this->settingService->get('shop_logo')
            );
        } elseif ($request->boolean('remove_logo')) {
            $this->settingService->removeLogo($this->settingService->get('shop_logo'));
            $data['shop_logo'] = null;
        }

        $this->settingService->setMany($data);

        $this->auditService->log(
            action: AuditLog::ACTION_SETTINGS,
            tableName: 'settings',
            oldValues: $old,
            newValues: $this->settingService->all()
        );

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Settings saved successfully.');
    }
}

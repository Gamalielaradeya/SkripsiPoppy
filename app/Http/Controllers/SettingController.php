<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\ThresholdSetting;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __invoke(): View
    {
        return view('settings.index', [
            'thresholdSettings' => ThresholdSetting::query()->orderBy('group')->orderBy('key')->get(),
            'systemSettings' => SystemSetting::query()->orderBy('group')->orderBy('key')->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        if ($request->has('settings')) {
            $validated = $request->validate([
                'settings' => ['required', 'array'],
                'settings.*.key' => ['required', 'string', 'max:255'],
                'settings.*.value' => ['nullable'],
            ]);

            foreach ($validated['settings'] as $setting) {
                Setting::set($setting['key'], $setting['value']);
            }
        } else {
            $excluded = ['logo', '_method'];
            foreach ($request->all() as $key => $value) {
                if (!in_array($key, $excluded) && !$request->hasFile($key)) {
                    Setting::set($key, $value);
                }
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('logos', 'public');
                Setting::set('library_logo', '/storage/' . $path);
            }
        }

        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'message' => 'Paramètres mis à jour.',
            'settings' => $settings,
        ]);
    }
}

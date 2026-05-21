<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteConfig;
use Illuminate\Http\Request;

class SiteConfigController extends Controller
{
    public function index()
    {
        $configs = SiteConfig::all()->pluck('value', 'key');
        return view('admin.site_config.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'navbar_color'    => 'required|string|max:7',
            'primary_color'   => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'theme_effect'    => 'required|string|in:normal,glossy',
        ]);

        foreach ($data as $key => $value) {
            SiteConfig::setVal($key, $value);
        }

        return back()->with('success', 'Konfigurasi situs berhasil diperbarui!');
    }

    public function reset()
    {
        SiteConfig::setVal('navbar_color',    '#2E7D32');
        SiteConfig::setVal('primary_color',   '#2E7D32');
        SiteConfig::setVal('secondary_color', '#729E3F');
        SiteConfig::setVal('theme_effect',    'normal');

        return back()->with('success', 'Konfigurasi situs dikembalikan ke default.');
    }
}

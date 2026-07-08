<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\SiteSetting;
use App\Models\Partner;

class SiteSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SiteSetting::class, 'site_setting');
    }

    // ---- Hero Section ----
    public function hero()
    {
        $this->authorize('update', SiteSetting::class);
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.site-settings.hero', compact('settings'));
    }

    public function updateHero(SiteSettingRequest $request)
    {
        $keys = ['hero_title_1', 'hero_title_2', 'hero_subtitle'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->validated($key));
            }
        }

        if ($request->hasFile('hero_image_file')) {
            $file = $request->file('hero_image_file');
            $filename = Str::random(40) . '_hero.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');
            SiteSetting::set('hero_image', Storage::url($path));
        }

        return redirect()->route('cms.site-settings.hero')->with('success', 'Hero section berhasil diperbarui.');
    }

    // ---- Workflow ----
    public function workflow()
    {
        $this->authorize('update', SiteSetting::class);
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.site-settings.workflow', compact('settings'));
    }

    public function updateWorkflow(SiteSettingRequest $request)
    {
        $keys = [
            'workflow_title', 'workflow_description',
            'workflow_step1_title', 'workflow_step1_desc',
            'workflow_step2_title', 'workflow_step2_desc',
            'workflow_step3_title', 'workflow_step3_desc',
        ];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->validated($key));
            }
        }

        return redirect()->route('cms.site-settings.workflow')->with('success', 'Alur kerja berhasil diperbarui.');
    }

    // ---- Schedule ----
    public function schedule()
    {
        $this->authorize('update', SiteSetting::class);
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.site-settings.schedule', compact('settings'));
    }

    public function updateSchedule(SiteSettingRequest $request)
    {
        $keys = ['schedule_description', 'schedule_days', 'schedule_hours', 'schedule_note'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->validated($key));
            }
        }

        if ($request->hasFile('schedule_image_file')) {
            $file = $request->file('schedule_image_file');
            $filename = Str::random(40) . '_schedule.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');
            SiteSetting::set('schedule_image', Storage::url($path));
        }

        return redirect()->route('cms.site-settings.schedule')->with('success', 'Jadwal operasional berhasil diperbarui.');
    }

    // ---- Partners ----
    public function partners()
    {
        $this->authorize('update', SiteSetting::class);
        $partners = Partner::ordered()->get();
        return view('admin.site-settings.partners', compact('partners'));
    }

    public function storePartner(SiteSettingRequest $request)
    {
        $validated = $request->validated();

        $file = $request->file('logo');
        $filename = Str::random(40) . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('images/logo', $filename, 'public');

        $order = Partner::max('order') + 1;

        Partner::create([
            'name' => $validated['name'],
            'logo_path' => Storage::url($path),
            'order' => $order,
        ]);

        return redirect()->route('cms.site-settings.partners')->with('success', 'Mitra kerja sama berhasil ditambahkan.');
    }

    public function destroyPartner(Partner $partner)
    {
        $this->authorize('deletePartner', SiteSetting::class);

        // Delete file from storage
        $storagePath = str_replace('/storage/', '', $partner->logo_path);
        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }

        $partner->delete();

        return redirect()->route('cms.site-settings.partners')->with('success', 'Mitra kerja sama berhasil dihapus.');
    }
}

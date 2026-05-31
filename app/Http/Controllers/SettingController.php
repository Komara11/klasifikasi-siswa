<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'school_name' => Setting::getValue('school_name', 'SMP Negeri 1 Sumber'),
            'academic_year' => Setting::getValue('academic_year', '2025/2026'),
            'school_logo' => Setting::getValue('school_logo'),
            'principal_name' => Setting::getValue('principal_name', 'Drs. H. Sudrajat, M.M.'),
            'principal_nip' => Setting::getValue('principal_nip', '19680312 199403 1 005'),
        ];

        $subjects = \App\Models\Subject::all();

        return view('admin.settings.index', compact('settings', 'subjects'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'principal_name' => 'required|string|max:255',
            'principal_nip' => 'nullable|string|max:50',
        ]);

        Setting::setValue('school_name', $request->school_name);
        Setting::setValue('academic_year', $request->academic_year);
        Setting::setValue('principal_name', $request->principal_name);
        Setting::setValue('principal_nip', $request->principal_nip);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        // Delete old logo if exists
        $oldLogo = Setting::getValue('school_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        Setting::setValue('school_logo', $path);

        return redirect()->route('admin.settings.index')->with('success', 'Logo sekolah berhasil diperbarui.');
    }

    public function deleteLogo()
    {
        $oldLogo = Setting::getValue('school_logo');
        if ($oldLogo) {
            if (Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::where('key', 'school_logo')->delete();
        }

        return redirect()->route('admin.settings.index')->with('success', 'Logo sekolah berhasil dihapus.');
    }

    public function updateWeights(Request $request)
    {
        $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0|max:5',
        ]);

        foreach ($request->weights as $subjectId => $weight) {
            \App\Models\Subject::where('id', $subjectId)->update(['weight' => $weight]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Bobot mata pelajaran berhasil diperbarui.');
    }
}

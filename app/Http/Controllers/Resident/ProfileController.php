<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $cuDan = auth('cudan')->user();
        $canHo = $cuDan?->canHoHienTai?->canHo;
        return view('resident.profile.show', compact('cuDan', 'canHo'));
    }

    public function edit()
    {
        $cuDan = auth('cudan')->user();
        return view('resident.profile.edit', compact('cuDan'));
    }

    public function update(Request $request)
    {
        $cuDan = auth('cudan')->user();

        $request->validate([
            'ho_ten_dem' => 'nullable|string|max:255',
            'ten'        => 'nullable|string|max:100',
            'sdt'        => 'nullable|string|max:20',
            'dia_chi'    => 'nullable|string|max:500',
            'tinh'       => 'nullable|string|max:100',
        ]);

        $data = $request->only('ho_ten_dem', 'ten', 'sdt', 'dia_chi', 'tinh');
        $data = array_filter($data, fn($v) => $v !== null);

        $cuDan->update($data);

        return redirect()->route('resident.profile.show')->with('success', 'Cập nhật hồ sơ thành công.');
    }
}

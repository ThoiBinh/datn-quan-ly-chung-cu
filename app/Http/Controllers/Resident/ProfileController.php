<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\CuDan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $cuDan = $user->cuDan;
        return view('resident.profile.show', compact('user', 'cuDan'));
    }

    public function edit()
    {
        $user = auth()->user();
        $cuDan = $user->cuDan;
        return view('resident.profile.edit', compact('user', 'cuDan'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'phone');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if ($user->cuDan) {
            $cuDan = $user->cuDan;
            $cuDanData = [];
            if ($request->filled('sdt')) $cuDanData['sdt'] = $request->sdt;
            if ($request->filled('que_quan')) $cuDanData['que_quan'] = $request->que_quan;
            if (!empty($cuDanData)) $cuDan->update($cuDanData);
        }

        return redirect()->route('resident.profile.show')->with('success', 'Cập nhật hồ sơ thành công.');
    }
}

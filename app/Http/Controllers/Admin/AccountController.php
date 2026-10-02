<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.account', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user)],
            'current_password' => ['required', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
        ]);

        if (! empty($data['new_password'])) {
            $user->password = $data['new_password']; // di-hash otomatis oleh cast 'hashed'
        }

        $user->save();

        return redirect()->route('admin.account.edit')->with('success', 'Akun berhasil diperbarui.');
    }
}

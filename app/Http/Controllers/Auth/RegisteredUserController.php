<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'umur' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|string|in:L,P',
            'status_pekerjaan' => 'nullable|string|max:255',
            'universitas' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'umur' => $request->umur,
            'gender' => $request->gender,
            'status_pekerjaan' => $request->status_pekerjaan,
            'universitas' => $request->universitas,
            'fakultas' => $request->fakultas,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'role' => 'nasabah',
            'status' => 'pending', // Pending verification by admin
            'saldo' => 0,
        ]);

        event(new Registered($user));

        return redirect(route('login', absolute: false))
            ->with('status', 'Pendaftaran berhasil. Akun Anda akan aktif setelah diverifikasi admin.');
    }
}

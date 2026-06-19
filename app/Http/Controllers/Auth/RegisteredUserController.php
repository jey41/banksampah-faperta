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
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'umur' => 'required|integer|min:1|max:150',
            'gender' => 'required|string|in:L,P',
            'status_pekerjaan' => 'required|string|max:255',
            'pekerjaan_lainnya' => 'required_if:status_pekerjaan,lainnya|nullable|string|max:255',
            'universitas' => 'required_if:status_pekerjaan,mahasiswa,dosen,civitas_akademika|nullable|string|max:255',
            'fakultas' => 'required_if:status_pekerjaan,mahasiswa,dosen,civitas_akademika|nullable|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
        ]);

        $statusPekerjaan = $request->status_pekerjaan;
        if ($statusPekerjaan === 'lainnya' && $request->filled('pekerjaan_lainnya')) {
            $statusPekerjaan = $request->pekerjaan_lainnya;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'umur' => $request->umur,
            'gender' => $request->gender,
            'status_pekerjaan' => $statusPekerjaan,
            'universitas' => in_array($request->status_pekerjaan, ['mahasiswa', 'dosen', 'civitas_akademika']) ? $request->universitas : null,
            'fakultas' => in_array($request->status_pekerjaan, ['mahasiswa', 'dosen', 'civitas_akademika']) ? $request->fakultas : null,
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

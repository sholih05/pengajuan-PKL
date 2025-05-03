<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // menampilkan view login
    public function index()
    {
        if (session()->has('nis')) {
            return redirect()->intended('d/siswa');// Ganti 'dashboard' dengan nama route yang ingin dituju
        }
        if (session()->has('id_guru')) {
            return redirect()->intended('d/guru');// Ganti 'dashboard' dengan nama route yang ingin dituju
        }
        if (session()->has('id_instruktur')) {
            return redirect()->intended('d/instruktur');// Ganti 'dashboard' dengan nama route yang ingin dituju
        }
        if (Auth::check()) {
            if (in_array(Auth::user()->role,[1,2])) {
                return redirect()->intended('dashboard');// Ganti 'dashboard' dengan nama route yang ingin dituju
            }
        }


        return view('auth.login');
    }

    // proses login
    public function authenticate(Request $request)
    {
        // Validasi input email dan password
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            'captcha' => ['required', 'captcha'],
        ]);

        // Menyusun kredensial dengan kondisi is_active
        $credentials['username'] = $request->username;
        $credentials['password'] = $request->password;

        // Melakukan percobaan autentikasi
        if (Auth::attempt($credentials)) {

            // Mendapatkan data pengguna yang telah login
            $user = Auth::user();

            // Periksa jika akun pengguna aktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'login_error' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
                ]);
            }

            if (Auth::user()->role == 2) {
                $id_jurusan = $user->guru->id_jurusan ?? null;
                $id_guru = $user->guru->id_guru ?? null;
                session(['id_jurusan' => $id_jurusan,'id_guru' => $id_guru]);

                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }else if (Auth::user()->role == 3) {
                $id_guru = $user->guru->id_guru ?? null;
                session(['id_guru' => $id_guru]);

                $request->session()->regenerate();
                return redirect()->intended('d/guru');
            }else if (Auth::user()->role == 4) {
                $id_instruktur = $user->instruktur->id_instruktur ?? null;
                $id_dudi = $user->instruktur->id_dudi ?? null;
                session(['id_instruktur' => $id_instruktur,'id_dudi' => $id_dudi]);

                $request->session()->regenerate();
                return redirect()->intended('d/instruktur');
            }else if (Auth::user()->role == 5) {
                $nis = $user->siswa->nis ?? null;
                $foto = $user->siswa->foto ?? null;
                session(['nis' => $nis,'foto' => $foto]);

                $request->session()->regenerate();
                return redirect()->intended('d/siswa');
            }else {
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }
        }

        // Jika autentikasi gagal
        return back()->withErrors([
            'login_error' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('username');
    }



    // proses log out
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // // menampilkan view forgot password
    // public function forgot_password()
    // {
    //     return view('auth.forgot-password');
    // }

    // // proses reset password
    // public function forgot_password_process(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => ['required', 'email'],
    //     ]);

    //     $credentials['is_active'] = true;
    //     // ....
    // }

    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    function coba() {
        $data = Presensi::with('siswa', 'guru')->whereHas('penempatan', function ($query) {
            $query->where('id_guru', '999999999999999');
        })->get();
        dd($data);

    }
}

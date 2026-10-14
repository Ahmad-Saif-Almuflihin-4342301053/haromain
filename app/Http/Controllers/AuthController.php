<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\UserLogin;
use App\Models\FiturLevel;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        // dd(Auth::user());
        if (Auth::check()) {
            return redirect('admin/dashboard');
        }

        // Jika belum login tampilkan form login
        return view('auth.login', [
            'menu' => 'Login'
        ]);
    }

    public function lupaPassword()
    {
        return view('auth.lupa-password', [
            'menu' => 'Lupa Password',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $token = $request->query('token');

        $reset_token = PasswordReset::where('token', $token)->first();
        $status = false;

        if ($reset_token) {
            $created_at = strtotime($reset_token->TglBuat);
            $expiration_time = 60 * 60; // 1 hour

            $validity = $created_at + $expiration_time;

            if (time() <= $validity) {
                $status = true;
            }
        }

        return view('auth.reset-password', [
            'menu'  => 'Reset Password',
            'status' => $status,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {

        $request->validate([
            'UserName' => 'required',
            'UserPsw'  => 'required'
        ]);


        $user = UserLogin::where('UserName', $request->UserName)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ]);
        }

        if (Hash::check($request->UserPsw, $user->UserPsw)) {
            if (Hash::needsRehash($user->UserPsw)) {
                $user->update([
                    'UserPsw' => Hash::make($request->UserPsw)
                ]);
            }
        } elseif ($user->UserPsw === md5($request->UserPsw)) {
            $user->update([
                'UserPsw' => Hash::make($request->UserPsw)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ]);
        }

        Auth::login($user);

        $fiturLevel = new FiturLevel;
        $menu = $fiturLevel->getMenuByLevel($user->LevelID);

        foreach ($menu as $m) {
            if ((int)$m->IsHasSubmenu === 1) {
                $m->Submenu = $fiturLevel->getSubmenuByLevel($user->LevelID, $m->FiturID);
            }
        }

        $travel = $user->IDTravel
            ? MstTravel::where('IDTravel', $user->IDTravel)->value('NamaTravel')
            : '';


        session([
            'Menu'       => $menu,
            'NamaTravel' => $travel
        ]);


        (new ServerLog)->insertLog($user->UserName, 'Login', 'Akun');

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'redirect' => url('admin/dashboard')
        ]);
    }

    public function logout()
    {

        $user = Auth::user();

        if ($user) {
            // Simpan log
            (new ServerLog)->insertLog(
                $user->UserName,
                'Logout',
                'Akun'
            );
        }

        Auth::logout();

        Session::flush();

        return redirect()->route('auth.login');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'UserNameOrHP' => 'required'
        ]);

        $UsernameOrHP = $request->UserNameOrHP;

        $userlogin = UserLogin::where('UserName', $UsernameOrHP)
            ->orWhere('HPNo', $UsernameOrHP)
            //->where('IsAktif', 1)
            ->first();

        if (!$userlogin) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Nomor HP tidak ditemukan'
            ]);
        }

        if (empty($userlogin->Email)) {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada data alamat email, silahkan hubungi administrator'
            ]);
        }

        try {

            $token = md5(uniqid());

            PasswordReset::create([
                'UserName' => $userlogin->UserName,
                'Token' => $token,
                'TglBuat' => Carbon::now(),
            ]);

            $appName = env('APP_NAME');
            $emailAdmin = env('MAIL_FROM_ADDRESS');

            $urlResetPass = URL::to('/auth/resetpassword?token=' . $token);


            Mail::send('emails.forgotpass', [
                'appName' => $appName,
                'NamaJamaah' => $userlogin->NamaLengkap,
                'urlResetPass' => $urlResetPass,
                'EmailAdmin' => $emailAdmin
            ], function ($message) use ($userlogin, $appName) {
                $message->to($userlogin->Email);
                $message->subject("Atur Ulang Kata Sandi Akun $appName");
            });

            return response()->json([
                'status' => true,
                'message' => "Permintaan atur ulang kata sandi berhasil dikirim ke alamat email {$userlogin->Email}. Silahkan buka kotak masuk email Anda"
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Permintaan atur ulang kata sandi gagal',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $password = $request->input('Password');
        $confirm  = $request->input('PasswordConfirm');
        $token    = $request->input('token');

        if ($password !== $confirm) {
            return response()->json([
                'status'  => false,
                'message' => 'Konfirmasi password tidak sesuai'
            ], 422);
        }

        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {
            return response()->json([
                'status'  => false,
                'message' => 'Token tidak valid'
            ], 404);
        }

        UserLogin::where('UserName', $passwordReset->UserName)
            ->update([
                'UserPsw' => Hash::make($password), 'IsAktif' => 1
            ]);

        PasswordReset::where('token', $token)->delete();

        return response()->json([
            'status'   => true,
            'message'  => 'Berhasil mengubah password',
            'redirect' => url('auth/login')
        ], 200);
    }
}

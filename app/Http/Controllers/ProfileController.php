<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $auth_service;

    public function __construct(AuthService $auth_service)
    {
        $this->auth_service = $auth_service;
    }

    public function index()
    {
        $token = session('token');

        if (!$token) return null;

        $user = $this->auth_service->getUserInfo();
        dd($user);
        return view('frontend.profile.user_profile', compact('user'));
    }

    public function changePassword()
    {
        $token = session('token');

        if (!$token) return null;

        $user = $this->auth_service->getUserInfo();

        return view('frontend.profile.ganti_password', compact('user'));
    }

    public function updateEmail(Request $request)
    {
        // Ambil email baru dari input form
        $newEmail = $request->input('new_email');

        // Kirim request ke API untuk update email
        $response = Http::withToken(session('token'))->put(config('api.base_url') . '/user/update-email', [
            'email' => $newEmail,
        ]);

        // Handle success response (200)
        if ($response->successful()) {
            // Ambil message dari response API
            $responseData = $response->json();
            $successMessage = $responseData['message'] ?? 'Link verifikasi telah dikirim ke email baru. Harap cek inbox Anda.';

            // Redirect back dengan success message
            return back()->with('success', $successMessage);
        }

        // Handle error response dari API
        $errorData = $response->json();
        $errorMessage = $errorData['message'] ?? 'Gagal memperbarui email.';

        // Jika ada error spesifik dari backend berdasarkan status code
        if ($response->status() === 429) {
            // Cooldown error - Too Many Requests
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } elseif ($response->status() === 400) {
            // Bad Request - Email sama atau sudah digunakan
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } elseif ($response->status() === 422) {
            // Unprocessable Entity - Validation error
            $errors = $errorData['errors'] ?? ['new_email' => $errorMessage];
            return back()->withErrors($errors)->withInput();
        } elseif ($response->status() === 500) {
            // Internal Server Error - Gagal kirim email
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } else {
            // General error untuk status code lainnya
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        }
    }
    public function updateAvatar(Request $request)
    {
        try {
            $response = Http::withToken(session('token'))->put(config('api.base_url') . '/user/avatar', [
                'avatar' => $request->input('avatar'),
            ]);

            if ($response->successful()) {
                $message = $response->json('message') ?? 'Avatar berhasil diperbarui.';
                // Redirect ke halaman profil (atau back ke halaman sebelumnya)
                return redirect()->back()->with('success', $message);
            }

            $errorMessage = $response->json('message') ?? 'Gagal memperbarui avatar.';
            return redirect()->back()->withErrors(['avatar' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('Update avatar error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['avatar' => 'Terjadi kesalahan.']);
        }
    }

    public function deleteAvatar(Request $request)
    {
        try {
            $response = Http::withToken(session('token'))->delete(config('api.base_url') . '/user/avatar');

            if ($response->successful()) {
                $message = $response->json('message') ?? 'Avatar berhasil dihapus.';
                return redirect()->back()->with('success', $message);
            }

            $errorMessage = $response->json('message') ?? 'Gagal menghapus avatar.';
            return redirect()->back()->withErrors(['avatar' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('Delete avatar error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['avatar' => 'Terjadi kesalahan.']);
        }
    }
}

<?php
namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Resend\Laravel\Facades\Resend;
use Illuminate\Support\Facades\Http;

class VerifyOtp extends Component
{
    public string $email = '';
    public string $code = '';
    public ?string $recaptchaToken = null;

    public function mount()
    {
        $this->email = session('otp_email');

        if (empty($this->email)) {
            return redirect()->route('login');
        }
    }

    public function verify()
    {
        $this->validate(['email'=>'required|email', 'code'=>'required|numeric|digits:6','recaptchaToken' => 'required',]);

        $response = Http::acceptJson()->post('http://127.0.0.1:9000/api/verify-2fa', [
            'email' => $this->email,
            'two_factor_code' => $this->code,
            'g-recaptcha-response' => $this->recaptchaToken
        ]);

        if ($response->failed()) {
            $this->addError('code', 'El código es incorrecto o ha expirado.');
            return;
        }

        $data = $response->json();
        $userFromApi = (object) $data['user'];

        $localUser = User::updateOrCreate(
            [
                'api_id' => $userFromApi->id
            ],
            [
                'name'      => $userFromApi->name,
                'last_name' => $userFromApi->last_name,
                'email'     => $userFromApi->email,
                'token'     => $data['token'] ,
                'rol_codigo'  => $userFromApi->rol_id
            ]
        );

        Auth::login($localUser);

        session(['api_token' => $data['token']]);

        return redirect()->intended(route('dashboard'));
    }

    public function resendCode()
    {
        $this->validate(['email' => 'required|email']);

        $response = Http::acceptJson()->post('http://127.0.0.1:9000/api/resend-2fa', [
            'email' => $this->email,
        ]);

        if ($response->successful()) {
            session()->flash('status', 'Se ha enviado un nuevo código a tu correo.');
        } else {
            $this->addError('email', 'No pudimos reenviar el código.');
        }
    }

    public function render() {
    return view('livewire.auth.verify-otp');
    }
}
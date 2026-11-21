<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'codigo' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'codigo.required' => 'Ingresa tu código de estudiante.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $credenciales['activo'] = true;

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            throw ValidationException::withMessages([
                'codigo' => 'El código o la contraseña son incorrectos, o la cuenta está inactiva.',
            ]);
        }

        $request->session()->regenerate();

        return $this->destinoTrasIngreso(Auth::user());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('login');
    }

    public function showCambiarClave(Request $request): View|RedirectResponse
    {
        /** @var User $usuario */
        $usuario = $request->user();

        if (! $usuario->puedeCambiarClave()) {
            return $this->destinoTrasIngreso($usuario)
                ->with('estado', 'Tu contraseña ya fue cambiada. Si la olvidaste, usa «Olvidé mi contraseña» en el ingreso.');
        }

        return view('auth.cambiar-clave');
    }

    public function updateClave(Request $request): RedirectResponse
    {
        /** @var User $usuario */
        $usuario = $request->user();

        if (! $usuario->puedeCambiarClave()) {
            return $this->destinoTrasIngreso($usuario)
                ->with('estado', 'Tu contraseña ya fue cambiada. Si la olvidaste, usa «Olvidé mi contraseña» en el ingreso.');
        }

        $datos = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.different' => 'La nueva contraseña debe ser diferente de la actual.',
        ]);

        $usuario->update([
            'password' => $datos['password'],
            'clave_cambiada_at' => now(),
        ]);

        return $this->destinoTrasIngreso($usuario)
            ->with('estado', 'Tu contraseña se actualizó correctamente.');
    }

    public function showOlvideClave(): View
    {
        return view('auth.olvide-clave');
    }

    public function enviarOlvideClave(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50'],
        ], [
            'codigo.required' => 'Ingresa tu código para continuar.',
        ]);

        $correo = strtolower($datos['codigo']).'@unmsm.edu.pe';

        $estado = Password::broker()->sendResetLink(
            ['email' => $correo],
            function (User $usuario, string $token) use ($correo, $request): string {
                $request->session()->flash('enlace_recuperacion', route('clave.restablecer', [
                    'token' => $token,
                    'correo' => $correo,
                ]));

                return Password::RESET_LINK_SENT;
            },
        );

        if ($estado !== Password::RESET_LINK_SENT) {
            return back()->withErrors([
                'codigo' => 'No pudimos preparar el enlace. Intenta nuevamente en unos minutos.',
            ]);
        }

        return back()->with('estado', 'Si el código existe, preparamos el enlace para tu correo san marquino.');
    }

    public function showRestablecerClave(Request $request, string $token): View
    {
        return view('auth.restablecer-clave', [
            'token' => $token,
            'correo' => (string) $request->query('correo', ''),
        ]);
    }

    public function restablecerClave(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        ]);

        $estado = Password::broker()->reset(
            $request->only('token', 'email', 'password', 'password_confirmation'),
            function (User $usuario, string $password): void {
                $usuario->update([
                    'password' => $password,
                    'clave_cambiada_at' => now(),
                ]);
            },
        );

        if ($estado !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => 'El enlace no es válido o ya venció. Solicita uno nuevo.',
            ]);
        }

        return to_route('login')->with('estado', 'Tu contraseña fue actualizada. Ahora ingresa con tu código.');
    }

    private function destinoTrasIngreso(User $usuario): RedirectResponse
    {
        if ($usuario->esAdministrador()) {
            return to_route('admin.index');
        }

        if ($usuario->esConductor()) {
            return to_route('conductor.index');
        }

        if ($usuario->esInvidente()) {
            return to_route('pasajero.invidente.index');
        }

        return to_route('pasajero.index');
    }
}

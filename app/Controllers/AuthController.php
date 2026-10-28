<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\ClienteRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UsuarioRepository;
use App\Support\Mailer;
use App\Support\Slug;
use App\Support\Upload;
use App\Support\Validator;

final class AuthController
{
    public function form(Request $peticion): Response
    {
        if (Auth::check()) {
            return redirigir(Auth::esPersonal() ? '/panel' : '/mi-cuenta');
        }

        return Response::html(view('paginas/entrar', [
            'titulo' => 'Entrar',
            'descripcion' => 'Entra a tu cuenta de Mikato para ver tus citas, mascotas e historial de estética.',
            'destino' => $this->destinoSeguro($peticion->cadena('destino')),
        ]));
    }

    public function entrar(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        $destino = $this->destinoSeguro($peticion->cadena('destino'));
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/entrar');
        }

        [$permitido, $espera] = Session::limitar('login', (int) config('limites.login_intentos', 8), (int) config('limites.login_ventana', 900));
        if (!$permitido) {
            flash('error', 'Demasiados intentos. Espera ' . ceil($espera / 60) . ' minutos e intenta de nuevo.');
            return redirigir('/entrar');
        }

        $login = $peticion->cadena('login');
        $password = (string) $peticion->input('password', '');
        if ($login === '' || $password === '') {
            flash('error', 'Escribe tu correo o usuario y tu contraseña.');
            Session::recordarViejo($peticion->cuerpo());
            return redirigir('/entrar');
        }

        $usuario = Auth::intentar($login, $password);
        if ($usuario === null) {
            flash('error', 'Los datos no coinciden. Revisa tu correo y contraseña.');
            Session::recordarViejo($peticion->cuerpo());
            return redirigir('/entrar');
        }

        Session::olvidarViejo();
        flash('exito', 'Bienvenido, ' . $usuario['nombre'] . '.');
        if ($destino !== '') {
            return redirigir($destino);
        }
        return redirigir(Auth::esPersonal() ? '/panel' : '/mi-cuenta');
    }

    public function registroForm(Request $peticion): Response
    {
        if (Auth::check()) {
            return redirigir(Auth::esPersonal() ? '/panel' : '/mi-cuenta');
        }

        return Response::html(view('paginas/registro', [
            'titulo' => 'Crear cuenta',
            'descripcion' => 'Crea tu cuenta gratuita para agendar, guardar el historial de tu perro y participar en la comunidad de estilistas.',
        ]));
    }

    public function registrar(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/registro');
        }

        [$permitido, $espera] = Session::limitar('registro', 5, 3600);
        if (!$permitido) {
            flash('error', 'Demasiados registros desde este dispositivo. Espera ' . ceil($espera / 60) . ' minutos.');
            return redirigir('/registro');
        }

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'correo' => mb_strtolower($peticion->cadena('correo')),
            'telefono' => $peticion->cadena('telefono'),
            'password' => (string) $peticion->input('password', ''),
            'password_confirm' => (string) $peticion->input('password_confirm', ''),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'required|email|max:160',
            'telefono' => 'max:30',
            'password' => 'required|min:8|max:100',
            'password_confirm' => 'required|same:password',
        ]);
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Escribe un correo válido.';
        }
        if (UsuarioRepository::existeCorreo($datos['correo'])) {
            $errores['correo'] = 'Ese correo ya tiene una cuenta. Entra o recupera tu acceso.';
        }
        if ($peticion->cadena('acepto') !== '1') {
            $errores['acepto'] = 'Acepta el aviso de privacidad para crear tu cuenta.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa los campos marcados.');
            return redirigir('/registro');
        }

        $base = explode('@', $datos['correo'])[0] ?? 'cliente';
        $usuario = Slug::unico($base, static fn (string $c): bool => UsuarioRepository::existeUsuario($c));
        $id = UsuarioRepository::crear([
            'nombre' => $datos['nombre'],
            'usuario' => $usuario,
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
            'rol' => 'cliente',
            'estado' => 'activo',
        ]);
        ClienteRepository::asegurarParaUsuario($id);
        Auth::intentar($datos['correo'], $datos['password']);
        Session::olvidarViejo();
        flash('exito', 'Tu cuenta está lista. Ya puedes agendar y guardar el historial de tu perro.');
        return redirigir('/mi-cuenta');
    }

    public function recuperarForm(Request $peticion): Response
    {
        if (Auth::check()) {
            return redirigir(Auth::esPersonal() ? '/panel' : '/mi-cuenta');
        }

        return Response::html(view('paginas/recuperar', [
            'titulo' => 'Recuperar acceso',
            'descripcion' => 'Solicita un enlace para restablecer la contraseña de tu cuenta de Mikato.',
        ]));
    }

    public function enviarRecuperacion(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        if ($peticion->cadena('sitio_web') !== '') {
            return redirigir('/entrar');
        }

        [$permitido, $espera] = Session::limitar('recuperar', 4, 3600);
        if (!$permitido) {
            flash('error', 'Demasiadas solicitudes. Espera ' . ceil($espera / 60) . ' minutos e intenta de nuevo.');
            return redirigir('/recuperar');
        }

        $correo = mb_strtolower(trim($peticion->cadena('correo')));
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Escribe un correo válido.');
            return redirigir('/recuperar');
        }

        $usuario = UsuarioRepository::porCorreo($correo);
        if ($usuario !== null && ($usuario['estado'] ?? '') === 'activo') {
            $id = (int) $usuario['id'];
            if (PasswordResetRepository::contarRecientes($id, 15) < 3) {
                $token = bin2hex(random_bytes(32));
                PasswordResetRepository::crear(
                    $id,
                    hash('sha256', $token),
                    $peticion->ip(),
                    (string) ($_SERVER['HTTP_USER_AGENT'] ?? '')
                );
                $enlace = url('/recuperar/' . $token);
                $cuerpo = "Hola " . $usuario['nombre'] . ":

"
                    . "Recibimos una solicitud para restablecer la contraseña de tu cuenta en Mikato Estilistas Caninos.

"
                    . "Abre este enlace (válido durante 60 minutos):
" . $enlace . "

"
                    . "Si no solicitaste este cambio, ignora este correo. Tu contraseña actual sigue funcionando.

"
                    . "Mikato Estilistas Caninos
"
                    . (string) config('url');
                Mailer::enviar(
                    $correo,
                    'Recupera tu contraseña · Mikato Estilistas Caninos',
                    $cuerpo,
                    (string) config('contacto.email')
                );
            }
        }

        flash('exito', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña. Revisa también spam.');
        return redirigir('/entrar');
    }

    /** @param array<string, string> $parametros */
    public function resetForm(Request $peticion, array $parametros): Response
    {
        $token = (string) ($parametros['token'] ?? '');
        $reset = $token !== '' ? PasswordResetRepository::porHash(hash('sha256', $token)) : null;
        if ($reset === null) {
            return Response::html(view('paginas/reset', [
                'titulo' => 'Enlace no válido',
                'descripcion' => 'El enlace para restablecer la contraseña venció o ya se usó.',
                'invalido' => true,
                'token' => '',
            ]), 410);
        }

        return Response::html(view('paginas/reset', [
            'titulo' => 'Nueva contraseña',
            'descripcion' => 'Elige una contraseña nueva para tu cuenta de Mikato.',
            'invalido' => false,
            'token' => $token,
            'nombre' => (string) $reset['nombre'],
        ]));
    }

    /** @param array<string, string> $parametros */
    public function resetPassword(Request $peticion, array $parametros): Response
    {
        Csrf::verificar($peticion);
        $token = (string) ($parametros['token'] ?? '');
        $reset = $token !== '' ? PasswordResetRepository::porHash(hash('sha256', $token)) : null;
        if ($reset === null) {
            flash('error', 'El enlace venció o ya se usó. Solicita uno nuevo.');
            return redirigir('/recuperar');
        }

        $nueva = (string) $peticion->input('password', '');
        $confirmar = (string) $peticion->input('password_confirm', '');
        if (mb_strlen($nueva) < 8) {
            flash('error', 'La contraseña debe tener al menos 8 caracteres.');
            return redirigir('/recuperar/' . $token);
        }
        if ($nueva !== $confirmar) {
            flash('error', 'Las contraseñas no coinciden.');
            return redirigir('/recuperar/' . $token);
        }

        UsuarioRepository::actualizarPassword((int) $reset['id'], password_hash($nueva, PASSWORD_DEFAULT));
        PasswordResetRepository::marcarUsado((int) $reset['reset_id']);
        PasswordResetRepository::invalidarTodos((int) $reset['id']);
        flash('exito', 'Contraseña actualizada. Ya puedes entrar.');
        return redirigir('/entrar');
    }

    public function salir(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::salir();
        flash('exito', 'Cerraste sesión.');
        return redirigir('/');
    }

    public function perfil(Request $peticion): Response
    {
        Auth::requiereLogin();
        $usuario = Auth::usuario();
        $cliente = ClienteRepository::porUsuario((int) $usuario['id']);

        return Response::html(view('paginas/perfil', [
            'titulo' => 'Mi perfil',
            'descripcion' => 'Actualiza tus datos, tu contraseña y tu avatar.',
            'usuario' => $usuario,
            'cliente' => $cliente,
        ]));
    }

    public function actualizarPerfil(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $id = (int) Auth::id();
        $actual = UsuarioRepository::porId($id);
        if ($actual === null) {
            return redirigir('/entrar');
        }

        $datos = [
            'nombre' => $peticion->cadena('nombre'),
            'correo' => mb_strtolower($peticion->cadena('correo')),
            'telefono' => $peticion->cadena('telefono'),
            'whatsapp' => $peticion->cadena('whatsapp'),
            'ciudad' => $peticion->cadena('ciudad'),
            'estado_geo' => $peticion->cadena('estado_geo'),
            'bio' => $peticion->cadena('bio'),
        ];
        $errores = Validator::validar($datos, [
            'nombre' => 'required|min:3|max:120',
            'correo' => 'required|email|max:160',
            'bio' => 'max:500',
        ]);
        $otro = UsuarioRepository::porCorreo($datos['correo']);
        if ($otro !== null && (int) $otro['id'] !== $id) {
            $errores['correo'] = 'Ese correo ya está en uso.';
        }
        if ($errores !== []) {
            Session::put('_errores', $errores);
            Session::recordarViejo($datos);
            flash('error', 'Revisa tus datos.');
            return redirigir('/perfil');
        }

        UsuarioRepository::actualizarPerfil($id, $datos);
        $archivo = $peticion->archivo('avatar');
        if ($archivo !== null && (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $subida = Upload::imagen($archivo, 'avatares');
            if ($subida['ok']) {
                UsuarioRepository::actualizarAvatar($id, (string) $subida['ruta']);
            } else {
                flash('error', $subida['error'] ?? 'No se pudo subir el avatar.');
            }
        }

        Auth::refrescar();
        Session::olvidarViejo();
        flash('exito', 'Perfil actualizado.');
        return redirigir('/perfil');
    }

    public function cambiarPassword(Request $peticion): Response
    {
        Csrf::verificar($peticion);
        Auth::requiereLogin();
        $id = (int) Auth::id();
        $actual = UsuarioRepository::porId($id);
        if ($actual === null) {
            return redirigir('/entrar');
        }

        $actualPassword = (string) $peticion->input('password_actual', '');
        $nueva = (string) $peticion->input('password_nueva', '');
        $confirmar = (string) $peticion->input('password_confirm', '');

        if (!password_verify($actualPassword, (string) $actual['password_hash'])) {
            flash('error', 'La contraseña actual no es correcta.');
            return redirigir('/perfil');
        }
        if (mb_strlen($nueva) < 8) {
            flash('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
            return redirigir('/perfil');
        }
        if ($nueva !== $confirmar) {
            flash('error', 'Las contraseñas nuevas no coinciden.');
            return redirigir('/perfil');
        }

        UsuarioRepository::actualizarPassword($id, password_hash($nueva, PASSWORD_DEFAULT));
        flash('exito', 'Contraseña actualizada.');
        return redirigir('/perfil');
    }

    private function destinoSeguro(string $destino): string
    {
        if ($destino === '' || !str_starts_with($destino, '/') || str_starts_with($destino, '//')) {
            return '';
        }
        return $destino;
    }
}

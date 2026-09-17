<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger vers le dashboard approprié
        if (Auth::check()) {
            return self::redirectToDashboard();
        }

        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Déterminer si c'est un email ou un username
        $isEmail = filter_var($request->email, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'username';

        // Validation supplémentaire pour le username
        if (!$isEmail && !preg_match('/^[A-Z0-9]{6}$/', $request->email)) {
            throw ValidationException::withMessages([
                'email' => ['Le nom d\'utilisateur doit être composé de 6 caractères alphanumériques (ex: PHEN54).'],
            ])->redirectTo(route('login'));
        }

        // Vérifier les identifiants
        $user = User::where($field, $request->email)->first();

        // Le mot de passe est vérifié même si l'utilisateur n'existe pas, afin de ne pas
        // révéler par le temps de réponse quels comptes existent.
        if (!$user || !Hash::check($request->password, $user->password)) {
            if (!$user) {
                Hash::make($request->password);
            }

            throw ValidationException::withMessages([
                'email' => ['Ces identifiants ne correspondent pas à nos enregistrements.'],
            ])->redirectTo(route('login'));
        }

        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte a été désactivé. Contactez l\'administrateur.'],
            ])->redirectTo(route('login'));
        }

        // Connecter l'utilisateur
        Auth::login($user, $request->boolean('remember'));

        // Régénérer l'ID de session après connexion (protection contre la fixation de session)
        $request->session()->regenerate();

        // Rediriger vers le dashboard approprié selon le type d'utilisateur
        return self::redirectToDashboard();
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Rediriger vers le dashboard approprié selon le type d'utilisateur
     */
    public static function redirectToDashboard()
    {
        return redirect(self::dashboardUrl());
    }

    /**
     * URL du dashboard correspondant au type d'utilisateur connecté
     */
    public static function dashboardUrl()
    {
        $user = Auth::user();

        if (!$user) {
            return route('login');
        }

        switch ($user->type) {
            case 'super_admin':
                return route('super-admin.dashboard');
            case 'company':
                return url('/company/dashboard');
            case 'hr':
            case 'paie':
            case 'payroll': // valeur écrite par le formulaire de création d'utilisateur
                return url('/hr/dashboard');
            case 'employee':
            default:
                return url('/employee/dashboard');
        }
    }

    /** 
     * Afficher le formulaire de mot de passe oublié
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Traiter la demande de réinitialisation de mot de passe
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email.',
            'email.email' => "L'adresse email n'est pas valide.",
        ]);

        // Lien envoyé par email (jeton Laravel, valable config auth.passwords.users.expire minutes).
        // Le code n'est jamais affiché, et le même message est rendu que le compte existe ou non :
        // on ne révèle pas quelles adresses sont inscrites.
        try {
            Password::broker()->sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::error('Envoi du lien de réinitialisation impossible : ' . $e->getMessage(), ['exception' => $e]);

            // En mode debug (serveur de développement), la cause technique est affichée pour faciliter le diagnostic
            $cause = config('app.debug') ? ' (' . class_basename($e) . ' : ' . Str::limit($e->getMessage(), 250) . ')' : '';

            return back()->withInput()->with('error', "L'email n'a pas pu être envoyé pour le moment. Réessayez dans quelques minutes." . $cause);
        }

        return back()->with('success', 'Si un compte correspond à cette adresse, un lien de réinitialisation vient de vous être envoyé. Il est valable ' . config('auth.passwords.users.expire') . ' minutes.');
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe (lien reçu par email)
     */
    public function showResetPasswordForm(Request $request, $code)
    {
        return view('auth.reset-password', [
            'code' => $code,
            'email' => old('email', $request->query('email')),
        ]);
    }

    /**
     * Traiter la réinitialisation de mot de passe
     */
    public function resetPassword(Request $request, $code)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Veuillez saisir votre adresse email.',
            'email.email' => "L'adresse email n'est pas valide.",
            'password.required' => 'Veuillez saisir un nouveau mot de passe.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
        ]);

        $statut = Password::broker()->reset(
            [
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $code,
            ],
            function ($user, $password) {
                // Le cast « hashed » du modèle chiffre le mot de passe ; les sessions « se souvenir de moi » sont invalidées
                $user->forceFill([
                    'password' => $password,
                    'password_code' => null,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($statut !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce lien de réinitialisation est invalide ou a expiré. Demandez-en un nouveau.']);
        }

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    public function register(Request $request, $pack) {

    }
}

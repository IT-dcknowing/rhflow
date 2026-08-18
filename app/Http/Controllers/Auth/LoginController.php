<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        if (!$user->isActive) {
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
            'email' => 'required|email|exists:users,email',
        ]);

        // Générer un code de réinitialisation
        $user = User::where('email', $request->email)->first();
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'password_code' => $resetCode,
        ]);

        // Ici vous pourriez envoyer un email avec le code
        // Pour l'instant, on affiche le code en développement
        return back()->with('success', "Code de réinitialisation : {$resetCode} (Envoi d'email à implémenter)");
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showResetPasswordForm($code)
    {
        $user = User::where('password_code', $code)->first();

        if (!$user) {
            abort(404, 'Code de réinitialisation invalide.');
        }

        return view('auth.reset-password', compact('code'));
    }

    /**
     * Traiter la réinitialisation de mot de passe
     */
    public function resetPassword(Request $request, $code)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('password_code', $code)->first();

        if (!$user) {
            abort(404, 'Code de réinitialisation invalide.');
        }

        $user->update([
            'password' => $request->password,
            'password_code' => null,
        ]);

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    public function register(Request $request, $pack) {

    }
}

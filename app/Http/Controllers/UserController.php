<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * EXEMPLES D'UTILISATION DU MODÈLE USER MIS À JOUR
     */

    public function examples()
    {
        // 1. Créer différents types d'utilisateurs
        $superAdmin = User::factory()->superAdmin()->create();
        $company = User::factory()->company()->create();
        $employee = User::factory()->employee()->create();
        $hrUser = User::factory()->hr()->create();

        // 2. Utiliser les accesseurs
        echo "Type de l'utilisateur : " . $superAdmin->type_label; // "Super Administrateur"
        echo "Est super admin ? " . ($superAdmin->is_super_admin ? 'Oui' : 'Non'); // "Oui"
        echo "Est actif ? " . ($company->is_active ? 'Oui' : 'Non'); // "Oui"

        // 3. Utiliser les scopes
        $activeUsers = User::active()->get(); // Tous les utilisateurs actifs
        $superAdmins = User::superAdmins()->get(); // Tous les super admins
        $companies = User::companies()->get(); // Toutes les entreprises
        $employees = User::employees()->get(); // Tous les employés
        $hrUsers = User::hrUsers()->get(); // Tous les utilisateurs RH

        // 4. Création manuelle avec les nouveaux champs
        $newUser = User::create([
            'name' => 'Jean Dupont',
            'username' => 'jean.dupont',
            'email' => 'jean.dupont@entreprise.com',
            'password' => 'motdepasse123',
            'type' => 'hr', // Responsable RH
            'lang' => 'fr',
            'plan' => 3,
            'storage_limit' => 50.00,
            'attendance_type' => 'biometrique',
            'messenger_color' => '#ff6b6b',
            'dark_mode' => 'auto',
        ]);

        // 5. Mettre à jour la dernière connexion
        $newUser->update([
            'last_login' => now(),
        ]);

        // 6. Récupérer l'URL de l'avatar
        $avatarUrl = $newUser->avatar_url; // Génère l'URL complète

        // 7. Vérifier les permissions
        if ($newUser->is_super_admin) {
            // Accès admin complet
        } elseif ($newUser->type === 'company') {
            // Gestion de l'entreprise
        } elseif (in_array($newUser->type, ['hr', 'paie', 'payroll'])) {
            // Gestion RH
        } else {
            // Interface employé
        }

        // 8. Recherche et filtrage avancés
        $users = User::where('type', 'company')
                    ->where('plan', '>', 2)
                    ->where('is_active', 1)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // 9. Gestion des couleurs personnalisées
        $userColors = [
            'primary' => $newUser->colorone,
            'secondary' => $newUser->colortwo,
            'messenger' => $newUser->messenger_color,
        ];

        // 10. Vérification abonnement
        if ($newUser->plan_expire_date && $newUser->plan_expire_date->isPast()) {
            // Abonnement expiré - restreindre l'accès
        }

        return response()->json([
            'message' => 'Exemples d\'utilisation du modèle User',
            'super_admin_count' => $superAdmins->count(),
            'company_count' => $companies->count(),
            'employee_count' => $employees->count(),
            'new_user' => $newUser->only(['name', 'type', 'type_label', 'is_active']),
        ]);
    }
}

<?php

namespace Modules\LandingPage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Order;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class LandingPageController extends Controller
{
    public function index()
    {
        // Récupérer les plans triés par ordre personnalisé avec FIELD
        $customOrder = [1, 2, 3, 5, 6, 4, 100, 102]; // IDs dans l'ordre voulu
        
        $plans = Plan::where('is_active', 1)
                     ->whereIn('id', $customOrder)
                     ->orderByRaw('FIELD(id, ' . implode(',', $customOrder) . ')')
                     ->get();
        
        return view('landingpage::index', compact('plans'));
    }

    public function simulateur()
    {
        return view('landingpage::simulateur');
    }

    public function showContact()
    {
        return view('landingpage::contact');
    }

    public function storeContact(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'company' => 'nullable|string|max:255',
                'message' => 'required|string|max:5000',
                'accept' => 'accepted'
            ], [
                'name.required' => 'Le nom est obligatoire',
                'email.required' => 'L\'adresse email est obligatoire',
                'email.email' => 'L\'adresse email n\'est pas valide',
                'message.required' => 'Le message est obligatoire',
                'accept.accepted' => 'Vous devez accepter les conditions'
            ]);

            // Créer le contact
            $contact = \App\Models\Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company' => $validated['company'],
                'message' => $validated['message'],
                'accept' => true,
                'processed' => false,
                'notes' => null
            ]);

            // Envoyer l'email
            $emailBody = $this->buildContactEmailBody($validated, $contact->id);
            
            Mail::html($emailBody, function ($message) use ($validated) {
                $message->to('rhflow@dc-knowing.com')
                        ->subject('Nouveau contact - ' . $validated['name'])
                        ->from($validated['email'], $validated['name']);
            });

            return redirect()->back()->with('success', 'Votre message a été envoyé avec succès!');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du contact: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.');
        }
    }

    private function buildContactEmailBody($data, $contactId)
    {
        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
        $html .= '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">';
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">📋 Nouveau Contact - RHFlow</h2>';
        $html .= '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">ID Contact:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">#' . $contactId . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Nom:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['name']) . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['email']) . '</td></tr>';
        if (!empty($data['company'])) {
            $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Entreprise:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['company']) . '</td></tr>';
        }
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Message:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . nl2br(htmlspecialchars($data['message'])) . '</td></tr>';
        $html .= '</table>';
        $html .= '<div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;">';
        $html .= '<p style="margin: 0; color: #1976d2;">⚡ Ce contact a été enregistré dans la base de données RHFlow</p>';
        $html .= '</div>';
        $html .= '</div></body></html>';
        
        return $html;
    }

    /**
     * Afficher la page d'inscription
     */
    public function showRegistration(Request $request)
    {
        $planId = $request->get('plan_id');
        $plan = null;
        
        if ($planId) {
            $plan = Plan::find($planId);
            if (!$plan) {
                return redirect()->route('landingpage')->with('error', 'Plan non trouvé');
            }
        }
        
        return view('landingpage::register', compact('plan'));
    }

    /**
     * Traiter l'inscription et créer la commande
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            // Informations personnelles
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            
            // Informations entreprise
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_size' => 'required|string',
            
            // Plan et conditions
            'plan_id' => 'required|exists:plans,id',
            'terms' => 'accepted'
        ], [
            'name.required' => 'Le nom complet est obligatoire',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères',
            'username.required' => "Le nom d'utilisateur est obligatoire",
            'username.unique' => "Ce nom d'utilisateur est déjà utilisé",
            'username.max' => "Le nom d'utilisateur ne doit pas dépasser 255 caractères",
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'L\'adresse email n\'est pas valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'company_name.required' => 'Le nom de l\'entreprise est obligatoire',
            'company_name.max' => 'Le nom de l\'entreprise ne doit pas dépasser 255 caractères',
            'company_email.email' => 'L\'email de l\'entreprise n\'est pas valide',
            'company_email.max' => 'L\'email de l\'entreprise ne doit pas dépasser 255 caractères',
            'company_size.required' => 'La taille de l\'entreprise est obligatoire',
            'company_size.in' => 'La taille de l\'entreprise sélectionnée n\'est pas valide',
            'plan_id.required' => 'Veuillez sélectionner un plan d\'abonnement',
            'plan_id.exists' => 'Le plan sélectionné n\'est pas valide',
            'terms.accepted' => 'Vous devez accepter les conditions générales d\'utilisation'
        ]);

        try {
            $plan = Plan::findOrFail($validated['plan_id']);
            
            // Créer l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => 'company',
                'is_active' => false, // Inactif jusqu'au paiement
                'plan' => $plan->id,
                'plan_expire_date' => null,
                'currency' => 'XOF',
                'ip_server' => $request->ip()
            ]);

            // Créer l'entreprise
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $validated['company_name'],
                'email' => $validated['company_email'] ?: $validated['email'],
                'phone' => $validated['company_phone'],
                'plan_id' => $plan->id,
                'address' => $validated['company_address'],
                'size' => $validated['company_size'],
                'max_employees' => $plan->max_employees,
                'is_active' => false // En attente de validation
            ]);

            // Créer la commande
            $order = Order::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'amount' => $plan->price,
                'currency' => 'XOF',
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total_amount' => $plan->price,
                'payment_method' => 'mobile_money',
                'notes' => 'Commande créée depuis l\'inscription. En attente de paiement.',
                'expires_at' => now()->addDays(7) // Expire dans 7 jours
            ]);

            // Envoyer les emails de notification
            $this->sendRegistrationEmails($user, $company, $order, $plan, $validated['password']);

            return redirect()->route('order.payment', $order)
                ->with('success', 'Votre compte a été créé avec succès! Veuillez procéder au paiement pour activer votre abonnement. Un mail vous a été envoyé pour suivre votre commande. Vérifiez aussi vos spams.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur technique est survenue. Veuillez réessayer ou contacter le support.');
        }
    }

    /**
     * Afficher la page de paiement
     */
    public function showPayment(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('order.success', $order);
        }

        return view('landingpage::payment', compact('order'));
    }

    /**
     * Page de succès après paiement
     */
    public function orderSuccess(Order $order, Request $request)
    {
        $validated = $request->validate([
            // Informations de paiement
            'payment_reference' => 'required|string',
            'payment_method' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'payment_comment' => 'nullable|string',
        ], [
            'payment_reference.required' => 'La référence de paiement est obligatoire',
            'payment_method.required' => 'Le mode de paiement est obligatoire',
            'payment_amount.required' => 'Le montant du paiement est obligatoire',
            'payment_amount.numeric' => 'Le montant doit être un nombre valide',
            'payment_amount.min' => 'Le montant doit être supérieur à 0',
        ]);
        
        try {
            $paymentReference = $validated['payment_reference'];
            $paymentMethod = $validated['payment_method'];
            $paidAt = now();
            $totalAmount = $validated['payment_amount'];
            $notes = $validated['payment_comment'] ?? null;
            
            // Mettre à jour la commande avec les informations de paiement
            $order->update([
                'payment_reference' => $paymentReference,
                'payment_method' => $paymentMethod,
                'paid_at' => $paidAt,
                'total_amount' => $totalAmount,
                'notes' => $notes,
                'status' => 'paid'
            ]);
            
            // Activer le compte de l'utilisateur et de l'entreprise
            $user = $order->user;
            $company = $user->company;
            $plan = $order->plan;
            
            if ($user && $company) {
                $user->update(['is_active' => true]);
                $company->update(['is_active' => true]);
                
                // Envoyer l'email de confirmation selon le type de plan
                $this->sendPaymentConfirmationEmail($user, $company, $order, $plan);
            }
            
            // Rediriger vers la page de succès en GET
            return redirect()->route('order.success.view', $order)
                ->with('success', 'Paiement confirmé avec succès! Votre compte a été activé.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur technique est survenue lors du traitement de votre paiement. Veuillez réessayer ou contacter le support.');
        }
    }

    /**
     * Afficher la page de succès (GET)
     */
    public function showOrderSuccess(Order $order)
    {
        return view('landingpage::order-success', compact('order'));
    }

    /**
     * Envoyer les emails de confirmation de paiement
     */
    private function sendPaymentConfirmationEmail(User $user, Company $company, Order $order, Plan $plan)
    {
        try {
            if ($plan->price == 0) {
                // Email pour plan gratuit
                $emailBody = $this->buildFreePlanEmail($user, $company);
                $subject = 'Bienvenue chez RH FLOW ! Votre gestion RH commence maintenant 🚀';
            } else {
                // Email pour plan payant
                $emailBody = $this->buildPaidPlanEmail($user, $company, $order, $plan);
                $subject = 'Bienvenue chez RH FLOW - Activation de votre compte Pro 🛡️';
            }
            
            Mail::html($emailBody, function ($message) use ($user, $subject) {
                $message->to($user->email)
                    ->subject($subject)
                    ->from('rhflow@dc-knowing.com', 'RHFlow');
            });

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Construire l'email pour plan gratuit
     */
    private function buildFreePlanEmail(User $user, Company $company)
    {
        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">';
        
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">Bonjour ' . htmlspecialchars($user->name) . ',</h2>';
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">Bienvenue dans la communauté RH FLOW ! Nous sommes ravis de vous accompagner dans la digitalisation de vos ressources humaines.</p>';
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">Votre compte a été créé avec succès sous le <strong>Plan Gratuit</strong>. Vous pouvez dès à présent vous connecter pour structurer votre gestion :</p>';
        
        $html .= '<ul style="line-height: 1.8; margin-bottom: 25px;">';
        $html .= '<li><strong>Identifiant :</strong> ' . htmlspecialchars($user->email) . '</li>';
        $html .= '<li><strong>Votre espace :</strong> <a href="' . url('/login') . '" style="color: #007bff; text-decoration: none;">Lien de connexion au logiciel</a></li>';
        $html .= '</ul>';
        
        $html .= '<h3 style="color: #333; margin-bottom: 15px;">Ce que vous pouvez faire dès maintenant :</h3>';
        $html .= '<ol style="line-height: 1.8; margin-bottom: 25px;">';
        $html .= '<li>Ajouter vos 5 premiers salariés.</li>';
        $html .= '<li>Configurer vos types de congés.</li>';
        $html .= '<li>Tester la centralisation de vos dossiers employés.</li>';
        $html .= '</ol>';
        
        $html .= '<p style="line-height: 1.6; margin-bottom: 30px;">Besoin d\'aide pour démarrer ? Notre équipe est à votre disposition pour vous guider.</p>';
        
        $html .= '<p style="margin-bottom: 10px;"><strong>L\'équipe RH FLOW</strong></p>';
        $html .= '<p style="font-style: italic; color: #666;">Gérez vos RH. Partout. À tout moment.</p>';
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Construire l'email pour plan payant
     */
    private function buildPaidPlanEmail(User $user, Company $company, Order $order, Plan $plan)
    {
        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; color: #333;">';
        
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">Bonjour ' . htmlspecialchars($user->name) . ',</h2>';
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">Nous vous remercions pour votre inscription à RH FLOW et pour la confiance que vous nous témoignez en choisissant le <strong>Plan Pro</strong>.</p>';
        
        $html .= '<h3 style="color: #333; margin-bottom: 15px;">Statut de votre demande :</h3>';
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">Comme indiqué lors de votre inscription, votre accès est actuellement en cours de traitement. Pour garantir la sécurité de votre compte et la conformité de votre abonnement, notre équipe procède à la vérification de votre paiement.</p>';
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">🕒 <strong>Délai d\'activation :</strong> 24 heures maximum.</p>';
        
        $html .= '<p style="line-height: 1.6; margin-bottom: 20px;">Dès que la vérification sera terminée, vous recevrez un e-mail de confirmation vous informant que toutes les fonctionnalités Pro (États de paie, Déclarations sociales, Support prioritaire) sont activées.</p>';
        
        $html .= '<h3 style="color: #333; margin-bottom: 15px;">Récapitulatif de votre commande :</h3>';
        $html .= '<ul style="line-height: 1.8; margin-bottom: 25px;">';
        $html .= '<li><strong>Plan :</strong> ' . htmlspecialchars($plan->name) . ' (' . number_format($order->total_amount, 0, ',', ' ') . ' FCFA / mois)</li>';
        $html .= '<li><strong>Utilisateur :</strong> ' . htmlspecialchars($user->email) . '</li>';
        $html .= '</ul>';
        
        $html .= '<p style="line-height: 1.6; margin-bottom: 30px;">Si vous avez la moindre question concernant votre activation, n\'hésitez pas à nous contacter directement au <strong>07 67 13 19 93</strong> ou par retour de mail.</p>';
        
        $html .= '<p style="line-height: 1.6; margin-bottom: 30px; font-style: italic;">Merci de votre patience et à très bientôt sur votre interface RH FLOW.</p>';
        
        $html .= '<p style="margin-bottom: 10px;"><strong>L\'équipe RH FLOW</strong></p>';
        $html .= '<p style="font-style: italic; color: #666;">Gérez vos RH. Partout. À tout moment.</p>';
        
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Envoyer les emails d'inscription
     */
    private function sendRegistrationEmails(User $user, Company $company, Order $order, Plan $plan, $password)
    {
        try {
            // Email à l'utilisateur
            $userEmailBody = $this->buildUserRegistrationEmail($user, $company, $order, $plan, $password);
            Mail::html($userEmailBody, function ($message) use ($user) {
                $message->to($user->email)
                    ->cc('infos@dcknowing.com')
                    ->cc('constant.keyman@dcknowing.com')
                    ->subject('Bienvenue sur RHFlow - Instructions de paiement')
                    ->from('rhflow@dc-knowing.com', 'RHFlow');
            });

            // Email à l'admin
            $adminEmailBody = $this->buildAdminNotificationEmail($user, $company, $order, $plan);
            Mail::html($adminEmailBody, function ($message) use ($order) {
                $message->to('rhflow@dc-knowing.com')
                    ->subject('Nouvelle inscription - Commande #' . $order->order_number)
                    ->from('rhflow@dc-knowing.com', 'RHFlow System');
            });

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails d\'inscription: ' . $e->getMessage());
        }
    }

    /**
     * Construire l'email pour l'utilisateur
     */
    private function buildUserRegistrationEmail(User $user, Company $company, Order $order, Plan $plan, $password)
    {
        $paymentReference = 'RHFLOW-' . $order->order_number;
        
        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
        $html .= '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; color: white;">';
        $html .= '<h1 style="margin: 0; font-size: 28px;">🎉 Bienvenue sur RHFlow!</h1>';
        $html .= '<p style="margin: 10px 0 0 0; opacity: 0.9;">Votre compte a été créé avec succès</p>';
        $html .= '</div>';
        
        $html .= '<div style="padding: 30px; background: #f8f9fa;">';
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">📋 Résumé de votre inscription</h2>';
        
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Nom:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($user->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($user->email) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Mot de passe:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($password) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Entreprise:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($company->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Plan choisi:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($plan->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Montant:</td><td style="padding: 10px; border-bottom: 1px solid #ddd; color: #28a745; font-weight: bold;">' . number_format($order->total_amount, 0, ',', ' ') . ' FCFA</td></tr>';
        $html .= '<tr><td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Numéro de commande:</td><td style="padding: 10px; border-bottom: 1px solid #ddd;">' . $order->order_number . '</td></tr>';
        $html .= '</table>';
        
        $html .= '<div style="text-align: center; margin-top: 30px;">';
        $html .= '<a href="' . route('login') . '" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">';
        $html .= '<i class="ri-arrow-right-line" style="margin-right: 8px;"></i>Se connecter';
        $html .= '</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '<div style="background: #333; color: white; padding: 20px; text-align: center; font-size: 12px;">';
        $html .= '<p style="margin: 0;">© 2024 RHFlow - Solution de gestion de paie</p>';
        $html .= '<p style="margin: 5px 0 0 0;">Cet email a été envoyé automatiquement. Merci de ne pas répondre.</p>';
        $html .= '</div>';
        $html .= '</body></html>';
        
        return $html;
    }

    /**
     * Construire l'email pour l'admin
     */
    private function buildAdminNotificationEmail(User $user, Company $company, Order $order, Plan $plan)
    {
        $html = '<html><body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
        $html .= '<div style="background: #dc3545; padding: 20px; text-align: center; color: white;">';
        $html .= '<h1 style="margin: 0; font-size: 24px;">🔔 Nouvelle inscription à traiter</h1>';
        $html .= '</div>';
        
        $html .= '<div style="padding: 20px; background: #f8f9fa;">';
        $html .= '<h2 style="color: #333; margin-bottom: 20px;">📋 Détails de la nouvelle inscription</h2>';
        
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Commande:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">#' . $order->order_number . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Client:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($user->name) . ' (' . htmlspecialchars($user->email) . ')</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Entreprise:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($company->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Taille:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($company->size) . '-' . htmlspecialchars($company->max_employees) . ' employés</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Plan:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($plan->name) . '</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Montant:</td><td style="padding: 8px; border-bottom: 1px solid #ddd; color: #28a745; font-weight: bold;">' . number_format($order->total_amount, 0, ',', ' ') . ' FCFA</td></tr>';
        $html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Statut:</td><td style="padding: 8px; border-bottom: 1px solid #ddd;"><span style="background: #ffc107; color: #333; padding: 2px 8px; border-radius: 3px; font-size: 12px;">En attente de paiement</span></td></tr>';
        $html .= '</table>';
        
        $html .= '<div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 15px;">';
        $html .= '<p style="margin: 0; color: #0c5460;"><strong>📱 Référence de paiement:</strong> RHFLOW-' . $order->order_number . '</p>';
        $html .= '</div>';
        
        $html .= '<div style="text-align: center; margin-top: 20px;">';
        $html .= '<a href="' . url('super-admin/commandes/' . $order->id) . '" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Voir la commande</a>';
        $html .= '</div>';
        
        $html .= '</div></body></html>';
        
        return $html;
    }

    public function showPrivacy(){
        return view('landingpage::privacy');
    }
}

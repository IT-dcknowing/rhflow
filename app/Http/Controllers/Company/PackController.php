<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Order;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackController extends Controller
{
    /**
     * Afficher la page des packs d'abonnement
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Récupérer les plans actifs
        $plans = Plan::active()->orderBy('price')->get();
        
        // Récupérer l'historique des commandes de l'utilisateur
        $orders = $user->orders()->with('plan')->latest()->get();
        
        return view('company.packs.pack', compact('plans', 'company', 'orders'));
    }

    /**
     * Obtenir les plans au format JSON
     */
    public function getPlans()
    {
        $plans = Plan::active()->orderBy('price')->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'formatted_price' => $plan->formatted_price,
                'duration' => $plan->duration,
                'max_users' => $plan->max_users,
                'max_employees' => $plan->max_employees,
                'storage_limit' => $plan->storage_limit,
                'features' => $plan->features_list,
                'popular' => $plan->popular,
                'enable_chatgpt' => $plan->enable_chatgpt,
            ];
        });
        
        return response()->json($plans);
    }

    /**
     * Afficher la page de paiement pour un plan
     */
    public function showPayment(Request $request, Plan $plan)
    {
        $action = $request->input('action', 'new');
        $duration = $request->input('duration', 1);
        
        $user = Auth::user();
        $company = $user->company;
        
        // Calculer le montant total
        $amount = $plan->price * $duration;
        
        // Créer ou mettre à jour la commande
        $order = $this->createOrUpdateOrder($plan, $user, $action, $duration, $amount);
        
        // Retourner la vue de paiement
        return view('company.packs.payment', [
            'plan' => $plan,
            'order' => $order,
            'action' => $action,
            'duration' => $duration,
            'amount' => $amount,
            'company' => $company
        ]);
    }

    /**
     * Traiter l'abonnement à un plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);
        
        DB::beginTransaction();
        
        try {
            // Créer la commande
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'total_amount' => $plan->price,
                'currency' => 'XOF',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => 'PAY-' . time() . '-' . Str::random(6),
                'phone' => $request->phone,
                'expires_at' => now()->addDays(7), // Expire dans 7 jours si non payé
            ]);

            // Mettre à jour l'entreprise si elle existe
            if ($user->company) {
                $user->company->update([
                    'plan_id' => $plan->id,
                    'subscription_status' => 'pending',
                ]);
            }

            DB::commit();

            // Rediriger vers la page de paiement appropriée
            if ($request->payment_method === 'wave') {
                return redirect()->route('order.payment', $order->id);
            } else {
                return redirect()->route('company.packs.index')
                    ->with('success', 'Commande créée avec succès. Procédez au paiement.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de la commande: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Renouveler l'abonnement actuel
     */
    public function renew(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:12',
            'payment_method' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $company = $user->company;
        
        // La relation s'appelle companyPlan ; $company->plan renvoyait null, donc
        // le renouvellement échouait systématiquement sur ce test.
        if (!$company || !$company->companyPlan) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas d\'abonnement actif à renouveler.');
        }

        $plan = $company->companyPlan;
        $duration = $request->duration;
        $amount = $plan->price * $duration;

        DB::beginTransaction();
        
        try {
            // Créer la commande de renouvellement
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $amount,
                'total_amount' => $amount,
                'currency' => 'XOF',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => 'RENEW-' . time() . '-' . Str::random(6),
                'phone' => $request->phone,
                'notes' => "Renouvellement pour {$duration} mois",
                'expires_at' => now()->addDays(7),
            ]);

            DB::commit();

            if ($request->payment_method === 'wave') {
                return redirect()->route('order.payment', $order->id);
            } else {
                return redirect()->route('company.packs.index')
                    ->with('success', 'Demande de renouvellement créée avec succès.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du renouvellement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Changer de plan (upgrade/downgrade)
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id|different:' . (Auth::user()->company->plan_id ?? 0),
            'payment_method' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $company = $user->company;
        $newPlan = Plan::findOrFail($request->plan_id);
        
        if (!$company) {
            return redirect()->back()
                ->with('error', 'Vous devez avoir une entreprise pour changer de plan.');
        }

        DB::beginTransaction();
        
        try {
            // Calculer le montant (prorata si nécessaire)
            $amount = $this->calculateUpgradeAmount($company, $newPlan);
            
            // Créer la commande de changement de plan
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'amount' => $amount,
                'total_amount' => $amount,
                'currency' => 'XOF',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => 'UPGRADE-' . time() . '-' . Str::random(6),
                'phone' => $request->phone,
                'notes' => 'Changement de plan: ' . (optional($company->companyPlan)->name ?: 'aucun') . ' → ' . $newPlan->name,
                'expires_at' => now()->addDays(7),
            ]);

            DB::commit();

            if ($request->payment_method === 'wave') {
                return redirect()->route('order.payment', $order->id);
            } else {
                return redirect()->route('company.packs.index')
                    ->with('success', 'Demande de changement de plan créée avec succès.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du changement de plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher l'historique des commandes
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        // Les filtres du formulaire etaient ignores : la requete ne les lisait pas.
        $query = $user->orders()
            ->with('plan')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->input('payment_method')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('date_to')));

        // Les compteurs portent sur l'ensemble filtre, pas sur la page affichee :
        // additionner $orders (10 lignes) donnait un total faux des la 2e page.
        $stats = [
            'total' => (clone $query)->count(),
            'paid' => (clone $query)->where('status', 'paid')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'amount' => (clone $query)->sum('total_amount'),
        ];

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('company.packs.history', compact('orders', 'stats'));
    }

    /**
     * Afficher la page de détails d'une commande
     */
    public function showOrderDetails(Order $order)
    {
        // Vérifier que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }

        return view('company.packs.order-details', compact('order'));
    }

    /**
     * Obtenir les détails d'une commande au format JSON
     */
    public function getOrderDetails(Order $order)
    {
        // Vérifier que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $orderData = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'status' => $order->status,
            'status_label' => $order->status_label,
            'status_color' => $order->status_color,
            'payment_method' => $order->payment_method,
            'payment_method_display' => $this->getPaymentMethodDisplay($order->payment_method),
            'payment_reference' => $order->payment_reference,
            'amount' => $order->amount,
            'amount_formatted' => number_format($order->amount, 0, ',', ' ') . ' FCFA',
            'discount_amount' => $order->discount_amount,
            'discount_formatted' => $order->discount_amount > 0 ? 
                '-' . number_format($order->discount_amount, 0, ',', ' ') . ' FCFA' : null,
            'total_amount' => $order->total_amount,
            'total_formatted' => number_format($order->total_amount, 0, ',', ' ') . ' FCFA',
            'plan_name' => $order->plan->name ?? 'Plan supprimé',
            'notes' => $order->notes,
            'expires_at' => $order->expires_at ? $order->expires_at->format('d/m/Y H:i') : null,
            'paid_at' => $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : null,
        ];

        return response()->json(['success' => true, 'order' => $orderData]);
    }

    /**
     * Annuler une commande
     */
    public function cancelOrder(Order $order)
    {
        // Vérifier que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        // Vérifier que la commande peut être annulée
        if (!$order->isPending() && !$order->isExpired()) {
            return response()->json([
                'success' => false, 
                'message' => 'Cette commande ne peut plus être annulée'
            ]);
        }

        try {
            $order->cancel('Annulée par l\'utilisateur');
            
            return response()->json([
                'success' => true, 
                'message' => 'Commande annulée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Une erreur est survenue lors de l\'annulation'
            ]);
        }
    }

    /**
     * Télécharger la facture d'une commande
     */
    public function downloadInvoice(Order $order)
    {
        // Vérifier que l'utilisateur est bien le propriétaire de la commande
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Non autorisé');
        }

        // Vérifier que la commande est été payée
        if (!$order->isPaid()) {
            abort(403, 'Facture non disponible pour les commandes non payées');
        }

        // Générer le PDF de la facture
        $pdf = $this->generateInvoicePDF($order);
        
        return $pdf->download('facture-' . $order->order_number . '.pdf');
    }

    /**
     * Créer ou mettre à jour une commande
     */
    private function createOrUpdateOrder(Plan $plan, $user, string $action, int $duration, float $amount)
    {
        // Vérifier s'il existe une commande en attente pour ce plan
        $existingOrder = $user->orders()
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            // Mettre à jour la commande existante
            $existingOrder->update([
                'amount' => $amount,
                'total_amount' => $amount,
                'notes' => $this->getOrderNotes($action, $duration),
                'expires_at' => now()->addDays(7),
            ]);
            
            return $existingOrder;
        }

        // Créer une nouvelle commande
        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $amount,
            'total_amount' => $amount,
            'currency' => 'XOF',
            'status' => 'pending',
            'payment_method' => 'wave', // Par défaut
            'payment_reference' => $this->generatePaymentReference($action),
            'notes' => $this->getOrderNotes($action, $duration),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Générer une référence de paiement
     */
    private function generatePaymentReference(string $action): string
    {
        $prefix = match($action) {
            'new' => 'SUB',
            'renew' => 'REN',
            'upgrade' => 'UPG',
            default => 'PAY'
        };
        
        return $prefix . '-' . time() . '-' . Str::random(6);
    }

    /**
     * Obtenir les notes de la commande
     */
    private function getOrderNotes(string $action, int $duration): string
    {
        return match($action) {
            'new' => "Nouvel abonnement pour {$duration} mois",
            'renew' => "Renouvellement pour {$duration} mois",
            'upgrade' => "Changement de plan",
            default => "Commande"
        };
    }

    /**
     * Calculer le montant pour un changement de plan
     */
    private function calculateUpgradeAmount(Company $company, Plan $newPlan): float
    {
        $currentPlan = $company->companyPlan;
        
        // Si c'est une première fois ou pas d'abonnement actif
        if (!$currentPlan || !$company->subscription_end_date) {
            return $newPlan->price;
        }

        // Jours restants, comptés depuis maintenant vers l'échéance. L'ordre compte :
        // en Carbon 3 diffInDays() est signé, et $echeance->diffInDays(now()) rendait
        // un nombre négatif pour une échéance à venir — donc jamais de crédit.
        $remainingDays = (int) now()->diffInDays($company->subscription_end_date->endOfDay(), false);
        
        if ($remainingDays <= 0) {
            return $newPlan->price;
        }
        
        // Calculer le crédit pour le temps restant
        $dailyRateCurrent = $currentPlan->price / 30; // Supposer 30 jours par mois
        $dailyRateNew = $newPlan->price / 30;
        
        $credit = $dailyRateCurrent * $remainingDays;
        $costForRemaining = $dailyRateNew * $remainingDays;
        
        // Calculer la différence
        $difference = $costForRemaining - $credit;
        
        return max(0, $difference);
    }

    /**
     * Obtenir l'affichage de la méthode de paiement
     */
    private function getPaymentMethodDisplay(string $method): string
    {
        return match($method) {
            'wave' => '<i class="ti ti-brand-wave text-primary"></i> Wave',
            'orange' => '<i class="ti ti-brand-orange text-warning"></i> Orange Money',
            'mtn' => '<i class="ti ti-brand-mtn text-success"></i> MTN Mobile Money',
            'moov' => '<i class="ti ti-brand-moov text-info"></i> Moov Money',
            default => ucfirst($method)
        };
    }

    /**
     * Générer le PDF de la facture
     */
    private function generateInvoicePDF(Order $order)
    {
        // Note: Cette méthode nécessite l'installation d'une librairie PDF comme DomPDF ou TCPDF
        // Pour l'instant, nous retournons une réponse simple
        
        $data = [
            'order' => $order,
            'company' => $order->user->company,
            'user' => $order->user,
            'plan' => $order->plan,
        ];
        
        // Créer une vue pour la facture
        $view = view('company.packs.invoice', $data)->render();
        
        // Pour l'instant, retourner une simple réponse HTML
        // En production, utilisez une vraie librairie PDF
        return response()->make($view, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-' . $order->order_number . '.pdf"'
        ]);
    }
}

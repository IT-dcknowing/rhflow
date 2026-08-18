<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Password;
use WhichBrowser\Parser;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Models\ExperienceCertificate;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\NOC;
use App\Models\TempToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Notification;

use App\Models\Company;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\Department;
use App\Models\Allowance;
use App\Models\MaritalStatus;
use App\Models\AllowanceOption;
use App\Models\JobCategorie;
use App\Models\PaieExercice;
use App\Models\PaiePeriode;
use App\Models\PaymentType;
use App\Models\Sector;
use Modules\Loans\Models\LoanPayment;
use Modules\Contracts\Models\Contract;
use Modules\Contracts\Models\ContractAttechements;
use Modules\Contracts\Models\ContractAvenant;
use Modules\Contracts\Models\ContractType;
use Modules\Employees\Models\Employee;
use Modules\Employees\Models\Demande;
use Modules\Employees\Models\EmployeeCmu;
use Modules\Employees\Models\EmployeeDocument;
use Modules\Employees\Models\Family;
use Modules\PaieSalaries\Models\PaySlip;
use Modules\Evenements\Models\Announcement;
use Modules\Evenements\Models\Award;
use Modules\Evenements\Models\AwardType;
use Modules\Evenements\Models\Event;
use Modules\Evenements\Models\Promotion;
use Modules\Ruptures\Models\Rupture;
use Modules\Evenements\Models\Transfer;
use Modules\Evenements\Models\EventEmployee;
use Modules\Evenements\Models\EventParticipant;
use Modules\Evenements\Models\Meeting;
use Modules\PaieSalaries\Models\Retenue;
use Modules\PaieSalaries\Models\TypeRetenue;
use Modules\PaieSalaries\Models\SetSalarie;
use Modules\NatureAvantage\Models\Avantage;
use Modules\Leaves\Models\Leave;
use Modules\Leaves\Models\LeaveType;
use Modules\Loans\Models\Loan;
use Modules\Loans\Models\LoanOption;
use Modules\Time\Models\TimeSheet;
use Modules\Time\Models\Overtime;
use Modules\Time\Models\Pointeuse;
use Modules\Loans\Services\LoanCalculatorService;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Models\WorkLocation;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\RateLimiter;

class ApiAppController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    /**
     * Vérifier le type d'utilisateur
     */
    public function checkUserType(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:employee,employe,company',
            'Type' => 'sometimes|string|in:employee,employe,company'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer l'utilisateur connecté via JWT
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get user type from request (support both 'type' and 'Type')
            $requestedType = $request->input('type') ?: $request->input('Type');

            // Normaliser le type (employe -> employee)
            if ($requestedType === 'employe') {
                $requestedType = 'employee';
            }

            // Vérifier le type d'utilisateur
            if ($user->type !== $requestedType) {
                Log::warning('User type verification failed', [
                    'expected' => $requestedType,
                    'actual' => $user->type,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'User type mismatch',
                    'expected_type' => $requestedType,
                    'actual_type' => $user->type
                ], 403);
            }

            return response()->json([
                'status' => true,
                'message' => 'User type verified successfully',
                'user_type' => $user->type
            ], 200);

        } catch (JWTException $e) {
            Log::error('JWT Exception during type verification', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Token validation failed'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Unexpected error during type verification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Alias pour checkUserType - pour compatibilité avec les routes existantes
     */
    public function verifyUserType(Request $request)
    {
        return $this->checkUserType($request);
    }

    public function loginCode(Request $request)
    {

        $credentials = $request->only('username', 'password_code');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }    // public function login(Request $request)
// {


    //     $validator = Validator::make($request->all(), [
//         'email' => 'required|email',
//         'password' => 'required|string|min:6'
//     ]);
    //     if ($validator->fails()) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Validation failed',
//             'errors' => $validator->errors()
//         ], 422);
//     }



    //     $loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) 
//         ? 'email' 
//         : 'username';


    //     $credentials = [
//         $loginField => $request->input('email'),
//         'password' => $request->input('password')
//     ];

    //     $key = 'login-attempts:' . $request->ip();


    //     if (RateLimiter::tooManyAttempts($key, 5)) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Too many login attempts. Please try again in 5 minutes.'
//         ], 429);
//     }
    //     try {
//         if (!$token = JWTAuth::attempt($credentials)) {


    //             RateLimiter::hit($key, 300); 


    //             Log::warning('Failed login attempt', [
//                 'email' => $request->input('email'),
//                 'ip' => $request->ip()
//             ]);


    //             return response()->json([
//                 'status' => false,
//                 'message' => 'Invalid credentials'
//             ], 401);
//         }



    //         RateLimiter::clear($key);



    //         $user = Auth::user();


    //         Log::info('Successful login', [
//             'user_id' => $user->id,
//             'email' => $user->email
//         ]);


    //         return response()->json([
//             'status' => true,
//             'message' => 'Login successful',
//             'token' => $token,
//             'user' => [
//                 'id' => $user->id,
//                 'name' => $user->name,
//                 'email' => $user->email,
//                 'type' => $user->type
//             ]
//         ], 200);


    //     } catch (JWTException $e) {
//         Log::error('JWT Exception during login', [
//             'error' => $e->getMessage(),
//             'email' => $request->input('email')
//         ]);


    //         return response()->json([
//             'status' => false,
//             'message' => 'Could not create token'
//         ], 500);
//     }
// }
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:username|string',
            'username' => 'required_without:email|string',
            'password' => 'required|string|min:6',
            // 'type' => 'sometimes|string|in:employee,employe,company',
            // 'Type' => 'sometimes|string|in:employee,employe,company'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get user type from request (support both 'type' and 'Type')
        $requestedType = $request->input('type') ?: $request->input('Type');

        // Normaliser le type (employe -> employee)
        if ($requestedType === 'employe') {
            $requestedType = 'employee';
        }

        // Determine login field (email or username)
        $loginField = $request->has('email') && filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $loginValue = $request->input('email') ?: $request->input('username');

        // Add rate limiting
        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'status' => false,
                'message' => 'Too many login attempts. Please try again in 5 minutes.'
            ], 429);
        }

        try {
            // Préparer les identifiants pour Auth::attempt
            $credentials = [
                $loginField => $loginValue,
                'password' => $request->input('password')
            ];

            // Utiliser Auth::attempt pour l'authentification
            if (!Auth::attempt($credentials)) {
                RateLimiter::hit($key, 300); // 5 minutes lockout

                Log::warning('Failed login attempt', [
                    'field' => $loginField,
                    'value' => $loginValue,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            // Vérifier le type d'utilisateur si spécifié
            if ($requestedType && $user->type !== $requestedType) {
                Auth::logout();
                RateLimiter::hit($key, 300);

                Log::warning('User type mismatch', [
                    'expected' => $requestedType,
                    'actual' => $user->type,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials - Wrong user type'
                ], 401);
            }

            // ✅ Authentification réussie - Générer le token JWT
            $token = JWTAuth::fromUser($user);

            if (!$token) {
                Log::error('JWT token generation failed', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Could not create token'
                ], 500);
            }

            // Clear rate limit on successful login
            RateLimiter::clear($key);

            Log::info('Successful login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'type' => $user->type
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username
                ]
            ], 200);

        } catch (JWTException $e) {
            Log::error('JWT Exception during login', [
                'error' => $e->getMessage(),
                'loginValue' => $loginValue,
                'requestedType' => $requestedType
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Could not create token'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error during login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     * Invalide le token JWT
     */
    public function logout()
    {
        try {
            // Récupère le token
            $token = JWTAuth::getToken();

            if ($token) {
                // Invalide le token
                JWTAuth::invalidate($token);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Déconnexion réussie'
                ]);
            }

        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired'
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid'
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is missing'
            ], 401);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Erreur déconnexion, réessayer plus tard.'
        ], 500);
    }

    /**
     * Récupère l'utilisateur connecté
     */

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['token_absent'], 401);
        }

        return response()->json(compact('user'));
    }

    public function forgotPassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Valider l'email reçu
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Générer un token de reset
        $token = Str::random(60);

        // Enregistrer le token dans la table password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Envoyer l'email avec le lien de réinitialisation
        Mail::to($user->email)->send(new ResetPasswordMail($token, $user->email));

        return response()->json([
            'message' => 'Un email de réinitialisation a été envoyé si l\'adresse existe.'
        ]);
    }

    public function userData(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $userData = User::where('id', $user->id)->first();
            return response()->json($userData);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur serveur lors de la récupération des données utilisateur.', 'message' => $e->getMessage()], 500);
        }
    }

    public function empData(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Chercher tous les employés liés à cet utilisateur, triés par ID (original en premier)
            $employees = Employee::where('user_id', $user->id)->orderBy('id', 'asc')->get();
            $count = $employees->count();

            if ($count > 1) {
                // S'il y a des doublons, on cherche prioritairement celui qui a un matricule
                $empData = Employee::join('users', 'employees.user_id', '=', 'users.id')
                    ->where('employees.user_id', $user->id)
                    ->whereNotNull('employees.employee_id')
                    ->where('employees.employee_id', '!=', '')
                    ->orderBy('employees.id', 'asc')
                    ->select('employees.*', 'employees.name as nom', 'employees.end_leave as endLeave', 'employees.email as emailemp', 'employees.phone as telephone', 'employees.address as adresse', 'users.avatar as avatar', 'users.username as username', 'users.type as type', 'employees.user_id as empid')
                    ->first();

                // Fallback par nom si aucun n'a de matricule pour cet user_id 
                // mais qu'une fiche complète existe (mismatch de user_id)
                if (!$empData || empty($empData->employee_id)) {
                    $empByName = Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->where('employees.name', 'like', '%' . $user->name . '%')
                        ->whereNotNull('employees.employee_id')
                        ->where('employees.employee_id', '!=', '')
                        ->orderBy('employees.id', 'asc')
                        ->select('employees.*', 'employees.name as nom', 'employees.end_leave as endLeave', 'employees.email as emailemp', 'employees.phone as telephone', 'employees.address as adresse', 'users.avatar as avatar', 'users.username as username', 'users.type as type', 'employees.user_id as empid')
                        ->first();

                    if ($empByName) {
                        $empData = $empByName;
                        \Log::info("[RH-FLOW-DEBUG] empData selected better match by name: " . $empData->id);
                    }
                }

                // Si on n'en trouve pas avec matricule non vide, on prend simplement le premier par ID
                if (!$empData) {
                    $empData = Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->where('employees.user_id', $user->id)
                        ->orderBy('employees.id', 'asc')
                        ->select(
                            'employees.*',
                            'employees.name as nom',
                            'employees.end_leave as endLeave',
                            'employees.email as emailemp',
                            'employees.phone as telephone',
                            'employees.address as adresse',
                            'users.avatar as avatar',
                            'users.username as username',
                            'users.type as type',
                            'employees.user_id as empid'
                        )
                        ->first();
                }
            } else {
                $empData = Employee::join('users', 'employees.user_id', '=', 'users.id')
                    ->where('employees.user_id', $user->id)
                    ->select(
                        'employees.*',
                        'employees.name as nom',
                        'employees.end_leave as endLeave',
                        'employees.email as emailemp',
                        'employees.phone as telephone',
                        'employees.address as adresse',
                        'users.avatar as avatar',
                        'users.username as username',
                        'users.type as type',
                        'employees.user_id as empid'
                    )
                    ->first();
            }

            // Ajouter une petite info de debug pour le mobile
            if ($empData) {
                $empData->nom = $empData->nom . " [UID:{$user->id}/EID:{$empData->id}]";
                $empData->debug_count = $count;
            }

            return response()->json($empData);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.', 'message' => $e->getMessage()], 500);
        }
    }

    public function changePassword($id, Request $request)
    {
        // Validation des données
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ]);

        try {
            // Récupération de l'utilisateur
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
            }

            // Vérification de l'ancien mot de passe
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['error' => 'Ancien mot de passe incorrect.'], 400);
            }

            // Mise à jour du mot de passe
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['success' => 'Mot de passe mis à jour avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue.', 'details' => $e->getMessage()], 500);
        }
    }

    public function empUpdate($id, Request $request)
    {
        try {
            //$user = JWTAuth::parseToken()->authenticate();
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'nullable|string',
                'logoBase64.data' => 'nullable|string',
                'logoBase64.mimeType' => 'nullable|string',
                'logoBase64.filename' => 'nullable|string',
            ]);

            $emp = Employee::where('user_id', '=', $id)->first();
            $emp->name = $request->name;
            $emp->email = $request->email;
            $emp->phone = $request->phone;
            $emp->address = $request->address;
            $emp->save();

            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }
            $user->email = $request->email;

            // Traitement de l'image en base64 si fournie
            if ($request->has('logoBase64.data') && !empty($request->input('logoBase64.data'))) {
                $imageData = $request->input('logoBase64.data');
                $mimeType = $request->input('logoBase64.mimeType');
                $filename = $request->input('logoBase64.filename');

                // Générer un nom de fichier unique
                $fileName = time() . '_' . pathinfo($filename, PATHINFO_FILENAME) . '.' . $this->getExtensionFromMimeType($mimeType);

                // Créer le dossier s'il n'existe pas
                $uploadPath = storage_path('uploads/avatar');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Sauvegarder l'image décodée
                $imagePath = $uploadPath . '/' . $fileName;
                file_put_contents($imagePath, base64_decode($imageData));

                // Mettre à jour le chemin dans la base de données
                $user->avatar = $fileName;
            }

            $user->save();

            return response()->json([
                'success' => 'Mise à jour réussie.',
                'data' => [
                    'user' => $user,
                    'employee' => $emp
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Mise à jour non réussie.',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Ajouter cette fonction utilitaire à votre contrôleur
    private function getExtensionFromMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    public function dashboardCompany(Request $request, $id)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            //$user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non connecté.'], 401);
            }

            if ($user->type == 'employee') {

                $emp = Employee::where('user_id', '=', $user->id)->select('id')->first();

                $announcements = Announcement::orderBy('announcements.id', 'desc')
                    ->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')
                    ->where('announcement_employees.employee_id', '=', $emp->id)
                    ->orWhere(
                        function ($q) {
                            $q->where('announcements.department_id', 0)->where('announcements.employee_id', 0);
                        }
                    )->get();

                $employees = Employee::get();
                $meetings = Meeting::orderBy('meetings.id', 'desc')->take(5)->leftjoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')->where('meeting_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q) {
                        $q->where('meetings.department_id', 0)->where('meetings.employee_id', 0);
                    }
                )->get();
                $events = Event::select('events.*', 'events.id as event_id', 'event_employees.*')->leftjoin('event_employees', 'events.id', '=', 'event_employees.event_id')->where('event_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q) {
                        $q->where('events.department_id', 0)->where('events.employee_id', 0);
                    }
                )->get();
                $arrEvents = [];

                foreach ($events as $event) {

                    $arr['id'] = $event['event_id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end'] = $event['end_date'];
                    $arr['className'] = $event['color'];
                    // $arr['borderColor']     = "#fff";
                    $arr['url'] = (!empty($event['event_id'])) ? route('eventsshow', $event['event_id']) : '0';
                    //  $arr['url']                = (!empty($event['event_id'])) ? route('eventsshow', $event['event_id']) : '0';

                    // $arr['textColor']       = "white";
                    $arrEvents[] = $arr;
                }

                return response()->json([
                    'arrEvents' => $arrEvents,
                    'announcements' => $announcements,
                    'employees' => $employees,
                    'meetings' => $meetings
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function profileEmployee(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
            }

            // Diagnostic approfondi : Si l'employé par ID est vide, chercher par nom
            $employee = Employee::where('user_id', $user->id)->orderBy('id', 'asc')->get()->sortByDesc(function ($e) {
                return !empty($e->employee_id);
            })->first();

            // Si l'employé trouvé par ID n'a pas de matricule, on tente une recherche par nom ET email
            // pour voir si une fiche existe sous un autre user_id (cas fréquent de doublons de comptes)
            if (!$employee || empty($employee->employee_id)) {
                // Tentative 1 : Par email (plus précis)
                $employeeByEmail = Employee::where('email', $user->email)
                    ->whereNotNull('employee_id')
                    ->where('employee_id', '!=', '')
                    ->first();

                if ($employeeByEmail) {
                    \Log::info("[RH-FLOW-DEBUG] Found better employee match by EMAIL: " . $employeeByEmail->id . " for user " . $user->id);
                    $employee = $employeeByEmail;
                } else {
                    // Tentative 2 : Par nom (si l'email diffère ou est absent)
                    $employeeByName = Employee::where('name', 'like', '%' . $user->name . '%')
                        ->whereNotNull('employee_id')
                        ->where('employee_id', '!=', '')
                        ->orderBy('id', 'asc')
                        ->first();

                    if ($employeeByName) {
                        \Log::info("[RH-FLOW-DEBUG] Found better employee match by NAME: " . $employeeByName->id . " for user " . $user->id);
                        $employee = $employeeByName;
                    }
                }
            }

            if (!$employee) {
                \Log::error("[RH-FLOW-DEBUG] TOTAL FAILURE for user " . $user->id . " (" . $user->name . ")");
                // On renvoie un objet "Dummy" avec l'erreur dans le nom pour que ce soit visible sur le mobile
                $dummy = new \stdClass();
                $dummy->id = 0;
                $dummy->name = "NOT_FOUND: " . $user->name . " (UID:" . $user->id . ")";
                $dummy->employee_id = "ERROR";
                $dummy->email = $user->email;

                return response()->json([
                    'employee' => $dummy,
                    'error_detail' => 'No employee record found even with fallback.',
                    'debug_user' => $user
                ], 200); // 200 pour éviter le fallback silencieux du mobile
            }
            $userInfo = User::where('id', '=', $user->id)->first();
            $categories = JobCategorie::where('id', $employee->categorie)->first();
            $contracts = Contract::where('employee_id', $employee->user_id)
                ->join('contract_types', 'contracts.type', '=', 'contract_types.id')
                ->first();
            $awards = Award::where('employee_id', $employee->id)->first();
            $transfers = Transfer::where('employee_id', $employee->id)->first();
            $promotions = Promotion::where('employee_id', $employee->id)->first();
            $maritalStatus = MaritalStatus::where('id', $employee->martalstatu_id)->first();
            $branches = Branch::where('id', $employee->branch_id)->first();
            $departments = Department::where('id', $employee->department_id)->first();
            $designations = Designation::where('id', $employee->designation_id)->first();
            $employeesId = $user->employeeIdFormat($employee->employee_id);

            return response()->json([
                'employee' => (function ($e, $u) {
                    $e->name = $e->name . " [UID:{$u->id}/EID:{$e->id}]";
                    return $e; })($employee, $user),
                'userInfo' => $userInfo,
                'categories' => $categories,
                'awards' => $awards,
                'transfers' => $transfers,
                'promotions' => $promotions,
                'maritalStatus' => $maritalStatus,
                'marital_status' => $maritalStatus, // Alias pour la compatibilité
                'branches' => $branches,
                'departments' => $departments,
                'designations' => $designations,
                'employeesId' => $employeesId,
                'contracts' => $contracts,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    public function getDossiersEmp(Request $request, $id)
    {
        // Trouver l'utilisateur
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        // Trouver l'employé
        $employee = Employee::where('user_id', '=', $user->id)->first();
        if (!$employee) {
            return response()->json(['error' => 'Aucun employé trouvé pour cet utilisateur.'], 404);
        }

        // Chaque requête secondaire est isolée — une erreur SQL n'empêche pas le reste
        $categories = null;
        try { $categories = JobCategorie::where('id', $employee->categorie)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp categories: ' . $e->getMessage()); }

        $contracts = null;
        try {
            $contracts = Contract::whereIn('employee_id', [$employee->user_id, $employee->id])
                ->join('contract_types', 'contracts.type_id', '=', 'contract_types.id')
                ->select('contracts.*', 'contract_types.name as name')
                ->first();
        } catch (\Exception $e) {
            \Log::warning('getDossiersEmp contracts join type_id: ' . $e->getMessage());
            try {
                // Fallback si la colonne s'appelle "type" au lieu de "type_id"
                $contracts = Contract::whereIn('employee_id', [$employee->user_id, $employee->id])
                    ->join('contract_types', 'contracts.type', '=', 'contract_types.id')
                    ->select('contracts.*', 'contract_types.name as name')
                    ->first();
            } catch (\Exception $e2) {
                \Log::warning('getDossiersEmp contracts join type: ' . $e2->getMessage());
                $contracts = Contract::whereIn('employee_id', [$employee->user_id, $employee->id])->first();
            }
        }

        $awards = null;
        try { $awards = Award::where('employee_id', $employee->id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp awards: ' . $e->getMessage()); }

        $transfers = null;
        try { $transfers = Transfer::where('employee_id', $employee->id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp transfers: ' . $e->getMessage()); }

        $promotions = null;
        try { $promotions = Promotion::where('employee_id', $employee->id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp promotions: ' . $e->getMessage()); }

        $maritalStatus = null;
        try { $maritalStatus = MaritalStatus::where('id', $employee->martalstatu_id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp maritalStatus: ' . $e->getMessage()); }

        $branches = null;
        try { $branches = Branch::where('id', $employee->branch_id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp branches: ' . $e->getMessage()); }

        $famille = [];
        try { $famille = Family::where('emp_id', $employee->id)->get(); } catch (\Exception $e) { \Log::warning('getDossiersEmp famille: ' . $e->getMessage()); }

        $documents = [];
        try { $documents = EmployeeDocument::where('employee_id', $employee->id)->get(); } catch (\Exception $e) { \Log::warning('getDossiersEmp documents: ' . $e->getMessage()); }

        $departments = null;
        try { $departments = Department::where('id', $employee->department_id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp departments: ' . $e->getMessage()); }

        $designations = null;
        try { $designations = Designation::where('id', $employee->designation_id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp designations: ' . $e->getMessage()); }

        $terminations = null;
        try { $terminations = Rupture::where('employee_id', $employee->id)->first(); } catch (\Exception $e) { \Log::warning('getDossiersEmp terminations: ' . $e->getMessage()); }

        $avenant = [];
        try {
            $avenant = ContractAvenant::select('contract_avenants.description as descrip', 'contract_avenants.type_avenant', 'contract_avenants.amount')
                ->join('contracts', 'contract_avenants.contract_id', '=', 'contracts.id')
                ->whereIn('contracts.employee_id', [$employee->user_id, $employee->id])
                ->get();
        } catch (\Exception $e) { \Log::warning('getDossiersEmp avenant: ' . $e->getMessage()); }

        // L'employé est TOUJOURS retourné — les champs secondaires peuvent être null
        $employeesId = $employee->employee_id ?? null;

        return response()->json([
            'employee'    => $employee,
            'categories'  => $categories,
            'awards'      => $awards,
            'transfers'   => $transfers,
            'promotions'  => $promotions,
            'famille'     => $famille,
            'documents'   => $documents,
            'maritalStatus' => $maritalStatus,
            'branches'    => $branches,
            'terminations' => $terminations,
            'departments' => $departments,
            'avenant'     => $avenant,
            'designations' => $designations,
            'employeesId' => $employeesId,
            'contracts'   => $contracts,
        ], 200);
    }


    public function getPayslips(Request $request, $id)
    {
        try {
            $user = $user = User::find($id);
            $employee = Employee::where('user_id', $user->id)->first();
            $payslips = PaySlip::where('employee_id', $employee->id)->get();
            return response()->json($payslips);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function indexTimesheet(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $timesheets = TimeSheet::where('employee_id', $employee->user_id)->get();
            return response()->json($timesheets);
        } catch (\Exception $e) {
            \Log::error('Erreur indexTimesheet: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur indexTimesheet : ' . $e->getMessage()], 500);
        }
    }

    public function CountTimesheetApprv(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $timesheets = TimeSheet::where('employee_id', $employee->user_id)->where('statut', 'approved')->count();
            return response()->json($timesheets);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function CountTimesheetPend(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $timesheets = TimeSheet::where('employee_id', $employee->user_id)->where('statut', 'pending')->count();
            return response()->json($timesheets);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function CountTimesheetRejc(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $timesheets = TimeSheet::where('employee_id', $employee->user_id)->where('statut', 'rejected')->count();
            return response()->json($timesheets);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function indexleave(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $leaves = Leave::select('leaves.*', 'leave_types.title')
                ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
                ->where('leaves.employee_id', $employee->id)
                ->get();
            return response()->json($leaves);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function indexAnnoucements(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $announcements = Announcement::orderBy('announcements.id', 'desc')
                ->where('announcements.company_id', '=', $employee->company_id)
                ->get();

            $events = Event::orderBy('events.id', 'desc')
                ->where('events.company_id', '=', $employee->company_id)
                ->where('events.color', '!=', 'event-danger')
                ->where('events.color', '!=', 'event-info')
                ->get();

            $meetings = Meeting::orderBy('meetings.id', 'desc')
                ->where('meetings.company_id', '=', $employee->company_id)
                ->get();

            return response()->json([
                'meetings' => $meetings,
                'announcements' => $announcements,
                'events' => $events,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getShowPointage(Request $request)
    {
        try {
            Carbon::setLocale('fr');

            // 1. Authentification
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json(['error' => 'Employé non trouvé'], 404);
            }

            // 2. Dates
            $startDate = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');

            // 3. Récupération des pointages
            try {
                $pointages = Pointeuse::where('emp_id', $employee->id)
                    ->whereBetween('auth_date', [$startDate, $endDate])
                    ->orderBy('auth_date', 'desc')
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Erreur SQL: ' . $e->getMessage());
                return response()->json(['error' => 'Erreur lors de la récupération des données'], 500);
            }

            // 4. Préparation de la structure des données
            $resultat = [];

            foreach ($pointages as $pointage) {
                $date = Carbon::parse($pointage->auth_date);
                $moisKey = $date->format('Y-m');
                $semaineKey = $date->format('W');
                $jourKey = $date->format('Y-m-d');

                // Initialisation de la structure du mois si elle n'existe pas
                if (!isset($resultat[$moisKey])) {
                    $resultat[$moisKey] = [
                        'mois' => ucfirst($date->translatedFormat('F Y')),
                        'semaines' => []
                    ];
                }

                // Initialisation de la structure de la semaine si elle n'existe pas
                if (!isset($resultat[$moisKey]['semaines'][$semaineKey])) {
                    $resultat[$moisKey]['semaines'][$semaineKey] = [
                        'numero_semaine' => $semaineKey,
                        'jours' => []
                    ];
                }

                // Initialisation de la structure du jour si elle n'existe pas
                if (!isset($resultat[$moisKey]['semaines'][$semaineKey]['jours'][$jourKey])) {
                    $resultat[$moisKey]['semaines'][$semaineKey]['jours'][$jourKey] = [
                        'date' => $date->format('d/m/Y'),
                        'jour_semaine' => ucfirst($date->translatedFormat('l')),
                        'pointages' => []
                    ];
                }

                // Ajout du pointage
                $resultat[$moisKey]['semaines'][$semaineKey]['jours'][$jourKey]['pointages'][] = [
                    'heure' => $pointage->auth_time,
                    'type' => $pointage->type,
                    'device' => $pointage->name_device
                ];
            }

            // 5. Transformation en tableaux indexés
            $resultatFinal = [];
            foreach ($resultat as $mois) {
                $moisData = [
                    'mois' => $mois['mois'],
                    'semaines' => []
                ];

                foreach ($mois['semaines'] as $semaine) {
                    $semaineData = [
                        'numero_semaine' => $semaine['numero_semaine'],
                        'jours' => array_values($semaine['jours'])
                    ];
                    $moisData['semaines'][] = $semaineData;
                }
                $resultatFinal[] = $moisData;
            }

            // 6. Horaires de bureau
            $company = Company::find($employee->company_id);
            $officeTime = [
                'startTime' => $company->company_start_time ?? ($company->settings['company_start_time'] ?? '08:00'),
                'endTime' => $company->company_end_time ?? ($company->settings['company_end_time'] ?? '17:00'),
            ];

            return response()->json([
                'pointages' => $resultatFinal,
                'officeTime' => $officeTime,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erreur générale: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des pointages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function documentsEmployee(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            //$documentIds = explode(',', $employee->documents);
            $documents = EmployeeDocument::where('employee_id', $employee->id)->get();
            return response()->json($documents);
        } catch (\Exception $e) {
            \Log::error('Erreur documentsEmployee: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur documentsEmployee : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Télécharger un document spécifique par son ID
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadDocument($id)
    {
        try {
            // Vérifier l'utilisateur
            $user = JWTAuth::parseToken()->authenticate();

            // Trouver le document
            $document = EmployeeDocument::findOrFail($id);

            // Gérer le cas où le libellé est vide
            $displayName = $document->libelle ?: 'document_' . $id;

            // Extraire le chemin du fichier
            $rawValue = $document->document_value;
            $filePath = $rawValue;

            // Si c'est du JSON (géré par EmployeesController), extraire le 'path'
            $decoded = json_decode($rawValue, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['path'])) {
                $filePath = $decoded['path'];
            }

            // Vérifier si le fichier existe
            if (!Storage::disk('public')->exists($filePath)) {
                // Essayer sans le préfixe 'public/' si présent
                $altPath = str_replace('public/', '', $filePath);
                if (Storage::disk('public')->exists($altPath)) {
                    $filePath = $altPath;
                } else {
                    \Log::error("Fichier introuvable sur le disque : " . $filePath);
                    return response()->json(['error' => 'Fichier introuvable sur le serveur'], 404);
                }
            }

            // Retourner le fichier en téléchargement
            return Storage::disk('public')->download($filePath, $displayName);

        } catch (\Exception $e) {
            \Log::error('Erreur downloadDocument: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du téléchargement : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un document spécifique par son ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDocument($id)
    {
        try {
            // Vérifier l'utilisateur
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json(['error' => 'Employé non trouvé'], 404);
            }

            // Trouver le document
            $document = EmployeeDocument::where('id', $id)
                ->where('employee_id', $employee->id)
                ->firstOrFail();

            // Extraire le chemin du fichier pour la suppression physique
            $rawValue = $document->document_value;
            $filePath = $rawValue;

            // Décodage si format JSON
            $decoded = json_decode($rawValue, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['path'])) {
                $filePath = $decoded['path'];
            }

            // Normaliser le chemin (enlever 'public/' si présent comme préfixe pour Storage::disk('public'))
            $normalizedPath = str_replace('public/', '', $filePath);

            // Supprimer le fichier physique s'il existe
            if (Storage::disk('public')->exists($normalizedPath)) {
                Storage::disk('public')->delete($normalizedPath);
            } elseif (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Supprimer l'enregistrement en base de données
            $document->delete();

            return response()->json([
                'status' => true,
                'message' => 'Document supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erreur deleteDocument: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Enregistrer un document pour un employé à partir de données base64
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDocumentBase64(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'nullable',
            'document_name' => 'required|string',
            'document_data' => 'required|string',
            'document_mime' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer l'utilisateur connecté
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employé non trouvé'
                ], 404);
            }

            // Déterminer l'extension du fichier
            $extension = pathinfo($request->document_name, PATHINFO_EXTENSION);
            if (empty($extension)) {
                if (strpos($request->document_mime, 'pdf') !== false) {
                    $extension = 'pdf';
                } elseif (strpos($request->document_mime, 'jpeg') !== false || strpos($request->document_mime, 'jpg') !== false) {
                    $extension = 'jpg';
                } elseif (strpos($request->document_mime, 'png') !== false) {
                    $extension = 'png';
                } else {
                    $extension = 'jpg';
                }
            }

            // Générer un nom de fichier unique
            $fileName = 'document_' . time() . '.' . $extension;
            $storagePath = 'documents/' . $fileName;

            // Décoder et sauvegarder le fichier
            $fileData = base64_decode($request->document_data);
            Storage::disk('public')->put($storagePath, $fileData);

            // Lier le document à l'employé
            $employeeDocument = new EmployeeDocument();
            $employeeDocument->employee_id = $employee->id;
            $employeeDocument->libelle = $fileName;
            $employeeDocument->document_value = $storagePath;
            $employeeDocument->company_id = $employee->company_id;
            $employeeDocument->save();

            return response()->json([
                'status' => true,
                'message' => 'Document enregistré avec succès',
                'data' => $employeeDocument
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du document: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l\'enregistrement du document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPayslipsLive(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $payslips = number_format(round($employee->get_net_salary()), 0, '.', ' ');
            return response()->json($payslips);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getPointageLive(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $pointages = Pointeuse::where('emp_id', $employee->id)->whereDate('auth_date', now())->orderby('id', 'desc')->first();
            return response()->json($pointages);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function CountHeuresAbsence(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();

            // Déterminer le début et la fin du mois en cours
            $startOfMonth = new \DateTime('first day of this month');
            $endOfMonth = new \DateTime('last day of this month');

            // Récupérer toutes les absences de l'employé pour le mois en cours
            $timesheets = TimeSheet::where('employee_id', $employee->user_id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('arrival_date', [$startOfMonth, $endOfMonth])
                ->get();

            $total_days = 0;
            $total_hours = 0;

            foreach ($timesheets as $timesheet) {
                // Calculer les jours d'absence
                $start = new \DateTime($timesheet->date);
                $end = new \DateTime($timesheet->arrival_date);
                $interval = $start->diff($end);
                $total_days += $interval->days + 1; // Ajouter 1 pour inclure le jour de début

                // Ajouter les heures d'absence
                $total_hours += $timesheet->hours;
            }

            return response()->json([
                'total_days' => $total_days,
                'total_hours' => $total_hours
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function MonthTrait(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $salaryData = PaySlip::where('employee_id', $employee->id)->orderBy('id', 'desc')->first();
            return response()->json($salaryData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function CountLoans(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            $loans = Loan::where('employee_id', $employee->id)->where('statut', 'running')->count();
            return response()->json($loans);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    /**
     * Créer une nouvelle demande de congé
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createLeaveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_type_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_reason' => 'required|string',
            'categorie_demandes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        // Récupérer l'ID de l'employé connecté
        $user = JWTAuth::parseToken()->authenticate();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employé non trouvé pour cet utilisateur'
            ], 404);
        }

        // Récupérer la période de paie active
        $periode = PaiePeriode::where('company_id', $employee->company_id)
            ->where('statut', 'en_cours')
            ->first();

        if (!$periode) {
            $periode = PaiePeriode::where('company_id', $employee->company_id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$periode) {
            return response()->json([
                'status' => false,
                'message' => 'Aucune période de paie trouvée.'
            ], 400);
        }

        // Gestion du fichier joint (base64)
        $filePath = null;
        if ($request->has('document_data') && !empty($request->document_data)) {
            try {
                $extension = 'jpg';
                if ($request->has('document_name')) {
                    $extension = pathinfo($request->document_name, PATHINFO_EXTENSION) ?: 'jpg';
                }

                $fileName = 'req_' . time() . '_' . rand(100, 999) . '.' . $extension;
                $storageDir = 'demandes';
                $fullPath = $storageDir . '/' . $fileName;

                $fileData = base64_decode($request->document_data);
                Storage::disk('public')->put($fullPath, $fileData);
                $filePath = 'storage/' . $fullPath;
            } catch (\Exception $e) {
                Log::error('Erreur upload base64: ' . $e->getMessage());
            }
        }

        $category = $request->input('categorie_demandes', 'absence');

        if ($category === 'pret' || $category === 'autre') {
            // Sauvegarder dans la table `demandes`
            $demande = new Demande();
            $demande->employee_id = $employee->id;
            $demande->periode_id = $periode->id;
            $demande->categorie_demandes = $category;
            $demande->demande_types = $request->leave_type_id;
            $demande->montant = $request->input('montant', 0);
            $demande->start_date = $request->start_date;
            $demande->end_date = $request->end_date;
            $demande->demande_reason = $request->leave_reason;
            $demande->status = 'Pending';
            $demande->company_id = $employee->company_id;
            $demande->file_path = $filePath;
            $demande->save();

            return response()->json([
                'status' => true,
                'message' => 'Demande de ' . ($category === 'pret' ? 'prêt' : 'autre') . ' soumise avec succès',
                'data' => $demande
            ], 201);

        } else {
            // Sauvegarder dans la table `leaves` (Absence)
            $start = new \DateTime($request->start_date);
            $end = new \DateTime($request->end_date);
            $interval = $start->diff($end);
            $total_days = $interval->days + 1;

            $leave = new Leave();
            $leave->employee_id = $employee->id;
            $leave->leave_type_id = $request->leave_type_id;
            $leave->periode_id = $periode->id;
            $leave->created_by = $user->id;
            $leave->applied_on = now();
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->total_leave_days = $total_days;
            $leave->leave_reason = $request->leave_reason;
            $leave->status = 'Pending';
            $leave->leave_statut = 1;
            $leave->company_id = $employee->company_id;
            // Note: La table leaves n'a peut-être pas de champ file_path d'origine, 
            // on pourrait la rajouter ou utiliser une table pivot si nécessaire.
            $leave->save();

            return response()->json([
                'status' => true,
                'message' => 'Demande de congé soumise avec succès',
                'data' => $leave
            ], 201);
        }
    }

    public function getLeaveTypes(Request $request)
    {
        $leaveTypes = LeaveType::all();

        return response()->json([
            'status' => true,
            'data' => $leaveTypes
        ]);
    }

    /**
     * Récupérer l'historique des demandes de congé de l'employé
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployeeLeaveHistory()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $employee_id = $user->employee->id;

        $leaves = Leave::where('employee_id', $employee_id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $leaves
        ]);
    }

    public function storeTimeSheet(Request $request)
    {
        // Validation des données
        $request->validate([
            'category_demande' => 'required|string',
            'type_demande' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'montant' => 'nullable|numeric'
        ]);

        // Récupérer l'ID de l'employé connecté
        $user = JWTAuth::parseToken()->authenticate();
        $employee_id = $user->id;
        $emp = Employee::where('user_id', '=', $employee_id)->first();

        // Calcul du nombre de jours d'absence
        $start = new \DateTime($request->date_debut);
        $end = new \DateTime($request->date_fin);
        $interval = $start->diff($end);
        $total_days = $interval->days + 1;

        // Création de la demande
        $demande = new Demande();
        $demande->employee_id = $emp->id;
        $demande->categorie_id = $request->category_demande;
        $demande->demande_type_id = $request->type_demande;
        $demande->start_date = $request->date_debut;
        $demande->end_date = $request->date_fin;
        $demande->demande_reason = $request->motif;
        // Gestion du montant selon le type de demande
        if ($request->category_demande === 'absence') {
            // Calcul de la retenue pour absence
            $bht = $emp->get_brut_salary();
            $heure_total = (40 * 52) / 12; // 173.33
            $taux_horaire = round($bht / $heure_total);
            $retenue_absence = $total_days * $taux_horaire;
            $demande->montant = round($retenue_absence);
        } elseif ($request->category_demande === 'pret') {
            // Pour les prêts, utiliser le montant saisi
            $demande->montant = $request->montant;
        } else {
            // Pour les autres types de demande
            $demande->montant = 0;
        }
        $demande->status = 'Pending';
        if ($request->hasFile('document')) {
            \Log::info('Document reçu: ' . $request->file('document')->getClientOriginalName());
            $filePath = $request->file('document')->store('justificatifs', 'public');
            \Log::info('Chemin du fichier: ' . $filePath);
            $demande->file_path = $filePath;
        } else {
            \Log::warning('Aucun document reçu');
            \Log::info('Contenu de la requête: ' . json_encode($request->all()));
        }
        $demande->company_id = $emp->company_id;

        $demande->save();

        return response()->json([
            'status' => true,
            'message' => 'Demande enregistrée avec succès',
            'data' => $demande
        ]);
    }

    public function demandeRequest(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee_id = $user->id;
            $emp = Employee::where('user_id', '=', $employee_id)->first();

            // Récupérer les demandes classiques
            $demandes = Demande::where('employee_id', $emp->id)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->categorie_id = $item->categorie_demandes; // Aliasing pour le frontend
                    $item->demande_type_id = $item->demande_types; // Aliasing pour le frontend
                    return $item;
                });

            // Récupérer les congés (leaves)
            $leaves = Leave::select('leaves.*', 'leave_types.title')
                ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
                ->where('leaves.employee_id', $emp->id)
                ->orderBy('leaves.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->categorie_id = 'absence';
                    $item->demande_type_id = $item->leave_type_id;
                    $item->demande_reason = $item->leave_reason;
                    return $item;
                });

            // Fusionner les deux listes
            $allDemandes = $demandes->concat($leaves)->sortByDesc('created_at')->values();

            return response()->json([
                'status' => true,
                'data' => $allDemandes
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function storeTimeSheetBase64(Request $request)
    {
        \Log::info('Réception demande avec fichier base64');

        // Validation des données
        $validator = Validator::make($request->all(), [
            'category_demande' => 'required|string',
            'type_demande' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string',
            'document_data' => 'required|string', // Données base64
            'document_name' => 'required|string',
            'document_type' => 'required|string',
            'montant' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer l'ID de l'employé connecté
            $user = JWTAuth::parseToken()->authenticate();
            $employee_id = $user->id;
            $emp = Employee::where('user_id', '=', $employee_id)->first();

            // Récupérer la période de paie active (en cours)
            $periode = PaiePeriode::where('company_id', $emp->company_id)
                ->where('statut', 'en_cours')
                ->first();

            // Fallback si aucune période n'est en cours, prendre la dernière créée
            if (!$periode) {
                $periode = PaiePeriode::where('company_id', $emp->company_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$periode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Aucune période de paie trouvée pour votre entreprise.'
                ], 400);
            }

            // Calcul du nombre de jours d'absence
            $start = new \DateTime($request->date_debut);
            $end = new \DateTime($request->date_fin);
            $interval = $start->diff($end);
            $total_days = $interval->days + 1;

            // Création de la demande
            $demande = new Demande();
            $demande->employee_id = $emp->id;
            $demande->periode_id = $periode->id;
            $demande->created_by = $user->id;
            $demande->categorie_demandes = $request->category_demande;
            $demande->demande_types = $request->type_demande;
            $demande->start_date = $request->start_date ?? $request->date_debut;
            $demande->end_date = $request->end_date ?? $request->date_fin;
            $demande->demande_reason = $request->demande_reason ?? $request->motif;

            // Gestion du montant selon le type de demande
            if ($request->category_demande === 'absence') {
                // Calcul de la retenue pour absence
                $bht = $emp->get_brut_salary();
                $heure_total = (40 * 52) / 12; // 173.33
                $taux_horaire = round($bht / $heure_total);
                $retenue_absence = $total_days * $taux_horaire;
                $demande->montant = round($retenue_absence);
            } elseif ($request->category_demande === 'pret') {
                // Pour les prêts, utiliser le montant saisi
                $demande->montant = $request->montant;
            } else {
                // Pour les autres types de demande
                $demande->montant = 0;
            }

            $demande->status = 'Pending';

            // Gestion du fichier en base64
            if ($request->document_data && $request->document_name) {
                \Log::info('Document reçu (base64): ' . $request->document_name);

                // Déterminer l'extension du fichier
                $extension = pathinfo($request->document_name, PATHINFO_EXTENSION);
                if (empty($extension)) {
                    // Essayer de déterminer l'extension à partir du type MIME
                    if (strpos($request->document_type, 'pdf') !== false) {
                        $extension = 'pdf';
                    } elseif (strpos($request->document_type, 'jpeg') !== false || strpos($request->document_type, 'jpg') !== false) {
                        $extension = 'jpg';
                    } elseif (strpos($request->document_type, 'png') !== false) {
                        $extension = 'png';
                    } else {
                        $extension = 'jpg'; // Par défaut
                    }
                }

                // Créer un nom de fichier unique
                $fileName = 'justificatif_' . time() . '.' . $extension;
                $storagePath = 'justificatifs/' . $fileName;

                // Décoder et sauvegarder le fichier
                $fileData = base64_decode($request->document_data);
                Storage::disk('public')->put($storagePath, $fileData);

                \Log::info('Chemin du fichier sauvegardé: ' . $storagePath);
                $demande->file_path = $storagePath;
            } else {
                \Log::warning('Aucun document reçu');
            }

            $demande->company_id = $emp->company_id;
            $demande->save();

            return response()->json([
                'status' => true,
                'message' => 'Demande enregistrée avec succès',
                'data' => $demande
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du traitement de la demande: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le bulletin de paie pour un employé
     *
     * @param string $employeeId
     * @param string $month
     * @return \Illuminate\Http\Response
     */
    public function viewBulletin($employeeId, $month)
    {
        try {
            // Vérifier que l'utilisateur connecté peut accéder au bulletin
            $user = JWTAuth::parseToken()->authenticate();

            $employeeId = $user->id;

            // Trouver l'employé
            $employee = Employee::where('user_id', '=', $employeeId)->first();

            // Vérifier les permissions (ajoutez votre logique de vérification)
            if ($user->id !== $employee->user_id) {
                return response()->json([
                    'message' => 'Accès non autorisé'
                ], Response::HTTP_FORBIDDEN);
            }

            // Rechercher le bulletin de paie
            $payslip = Payslip::where('employee_id', $employee->id)
                ->where('salary_month', $month)
                ->firstOrFail();

            // Chemin du fichier
            $filePath = "payslips/{$payslip->nom_etp}/Bulletin-{$employee->name}-{$month}.pdf";

            // Vérifier si le fichier existe
            if (!Storage::exists($filePath)) {
                return response()->json([
                    'message' => 'Bulletin de paie introuvable ou non disponible. Contactez votre RH'
                ], Response::HTTP_NOT_FOUND);
            }

            // Retourner le fichier PDF
            return response()->file(storage_path("app/{$filePath}"));

        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json([
                'message' => 'Erreur lors du chargement du bulletin',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*-------------------------------------------------------------------------
     //Entreprise Controller
     -------------------------------------------------------------------------*/

    public function getPayslipsCompany(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $payslips = PaySlip::select('pay_slips.*', 'employees.*', 'users.avatar as avatar')
                ->join('employees', 'pay_slips.employee_id', '=', 'employees.id')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->where('pay_slips.company_id', $user->company_id)
                ->get();

            return response()->json($payslips);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getlastPayslipsCompany(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $payslips = PaySlip::select('pay_slips.*', 'employees.*', 'users.avatar as avatar')
                ->join('employees', 'pay_slips.employee_id', '=', 'employees.id')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->where('pay_slips.company_id', $user->company_id)
                ->orderby('pay_slips.id', 'desc')
                ->first();

            return response()->json($payslips);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function announcementsEvents(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $announcements = Announcement::orderBy('announcements.id', 'desc')
                ->where('announcements.company_id', '=', $user->company_id)
                ->get();

            $events = Event::orderBy('events.id', 'desc')
                ->where('events.company_id', '=', $user->company_id)
                ->get();

            $meetings = Meeting::orderBy('meetings.id', 'desc')
                ->where('meetings.company_id', '=', $user->company_id)
                ->get();

            return response()->json([
                'meetings' => $meetings,
                'announcements' => $announcements,
                'events' => $events,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function demandeRequestCompany(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $company_id = $user->company_id;

            // 1. Récupérer les demandes classiques (prêts, autres)
            $demandes = Demande::where('demandes.company_id', $company_id)
                ->join('employees', 'demandes.employee_id', '=', 'employees.id')
                ->orderBy('demandes.id', 'desc')
                ->select('demandes.*', 'employees.name as employee_name')
                ->get()
                ->map(function ($item) {
                    $item->categorie_id = $item->categorie_demandes;
                    $item->demande_type_id = $item->demande_types;
                    return $item;
                });

            // 2. Récupérer les congés (leaves / absences)
            $leaves = Leave::select('leaves.*', 'leave_types.title', 'employees.name as employee_name')
                ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
                ->join('employees', 'leaves.employee_id', '=', 'employees.id')
                ->where('leaves.company_id', $company_id)
                ->orderBy('leaves.id', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->categorie_id = 'absence';
                    $item->demande_type_id = $item->leave_type_id;
                    $item->demande_reason = $item->leave_reason;
                    return $item;
                });

            // 3. Fusionner les deux listes
            $allDemandes = $demandes->concat($leaves)->sortByDesc('created_at')->values();

            return response()->json([
                'status' => true,
                'data' => $allDemandes
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur demandeRequestCompany: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur demandeRequestCompany : ' . $e->getMessage()], 500);
        }
    }

    public function getMassNetSalary(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $salaryData = PaySlip::where('company_id', $user->company_id)->orderBy('id', 'desc')->first();

            if (!$salaryData) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'netMassSalary' => 0,
                        'netEmpCharges' => 0,
                        'netCompCharges' => 0,
                        'cnps_total' => 0,
                        'cmu_total' => 0,
                        'employeesCount' => 0,
                    ],
                    'contracts' => [
                        'cdd' => 0,
                        'cdi' => 0,
                        'stages' => 0,
                        'autres' => 0,
                        'total' => 0,
                        'contractsEnd' => 0,
                        'contractsEndListe' => []
                    ]
                ], 200);
            }

            $employees = Employee::where('company_id', $user->company_id)->get();
            $paySlips = PaySlip::where('company_id', $user->company_id)
                ->where('salary_month', $salaryData->salary_month)
                ->get();

            $netMassSalary = 0;
            $netEmpCharges = 0;
            $netCompCharges = 0;
            $contractsEnd = 0;
            $cnps_total_emp = 0;
            $cnps_total_sal = 0;
            $cmu_total_emp = 0;
            $cmu_total_sal = 0;

            foreach ($paySlips as $paySlip) {
                $netMassSalary += $paySlip->net_payble;
                $netEmpCharges += $paySlip->imp_net;
                $netCompCharges += $paySlip->total_patronale;
                $cnps_total_emp += $paySlip->cnps_emp;
                $cnps_total_sal += $paySlip->cnps_sal;
                $cmu_total_emp += $paySlip->cmu_sal;
                $cmu_total_sal += $paySlip->cmu_emp;
            }
            // Début et fin du mois en cours
            $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

            // Compter les contrats se terminant ce mois-ci
            $contractsEnd = Contract::where('company_id', '=', $user->company_id)
                ->whereBetween('end_date', [$startOfMonth, $endOfMonth])
                ->count();

            // Liste des contrats se terminant ce mois-ci
            $contractsEndListe = Contract::where('contracts.company_id', '=', $user->company_id)
                ->join('employees', 'contracts.employee_id', '=', 'employees.user_id')
                ->select('contracts.*', 'employees.name as employee_id')
                ->whereBetween('contracts.end_date', [$startOfMonth, $endOfMonth])
                ->get();

            $contractsCDD = Contract::where('company_id', '=', Auth::user()->company_id)->where('status', '=', 'accept')->where('type_id', '=', '4')->count();
            $contractsCDI = Contract::where('company_id', '=', Auth::user()->company_id)->where('status', '=', 'accept')->where('type_id', '=', '3')->count();
            $contractsStages = Contract::where('company_id', '=', Auth::user()->company_id)->where('status', '=', 'accept')->where('type_id', '=', '2')->count();
            $contractsAutres = Contract::where('company_id', '=', Auth::user()->company_id)->where('status', '=', 'accept')->where('type_id', '>', '4')->count();

            // Retourner toutes les données dans un objet JSON structuré
            return response()->json([
                'status' => true,
                'data' => [
                    'netMassSalary' => round($netMassSalary),
                    'netEmpCharges' => round($netEmpCharges),
                    'netCompCharges' => round($netCompCharges),
                    'cnps_total' => round($cnps_total_emp + $cnps_total_sal),
                    'cmu_total' => round($cmu_total_emp + $cmu_total_sal),
                    'employeesCount' => $paySlips->count(),
                ],
                'contracts' => [
                    'cdd' => $contractsCDD,
                    'cdi' => $contractsCDI,
                    'stages' => $contractsStages,
                    'autres' => $contractsAutres,
                    'total' => $contractsCDD + $contractsCDI + $contractsStages + $contractsAutres,
                    'contractsEnd' => $contractsEnd,
                    'contractsEndListe' => $contractsEndListe
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur getMassNetSalary: ' . $e->getMessage());
            return response()->json(['status' => false, 'error' => 'Erreur lors du calcul de la masse salariale.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getEmployeesInfo(Request $request)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();
            $employees = Employee::where('employees.company_id', $user->company_id)
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->select('employees.*', 'users.avatar as avatar', 'branches.name as branch_name', 'designations.name as designation_name', 'departments.name as department_name', 'employees.salary_type')
                ->where('employees.is_active', 1)
                ->get();

            return response()->json([
                'status' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.'], 401);
        }
    }

    public function updateRequestStatus(Request $request, $id)
    {
        try {
            $status = $request->status;
            $category = $request->categorie_id; // Optionnel, envoyé par l'app mobile

            if ($category === 'absence') {
                $demande = Leave::find($id);
                // Mapper le statut pour la table leaves (enum en français)
                if ($status === 'Approved')
                    $status = 'Approuvé';
                if ($status === 'Rejected')
                    $status = 'Rejeté';
            } else {
                $demande = Demande::find($id);
            }

            if (!$demande) {
                return response()->json([
                    'status' => false,
                    'message' => 'Demande introuvable.'
                ], 404);
            }

            $demande->status = $status;
            $demande->save();

            return response()->json([
                'status' => true,
                'message' => 'Statut de la demande mis à jour avec succès.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur updateRequestStatus: ' . $e->getMessage());
            return response()->json(['error' => 'Statut de la demande non mis à jour: ' . $e->getMessage()], 400);
        }
    }

    public function empUpdateAvatar(Request $request)
    {
        // Validate sync token
        $syncToken = $request->input('sync_token');
        if (!$this->validateSyncToken($syncToken)) {
            return response()->json(['error' => 'Token invalide ou expiré'], 401);
        }

        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'avatar.required' => 'Veuillez sélectionner une image.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'L\'image doit être au format JPEG, PNG ou JPG.',
                'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            ]);

            if ($validator->fails()) {
                return view('avatar-update', [
                    'errors' => $validator->errors(),
                    'sync_token' => $syncToken
                ]);
            }

            // Find the authenticated user (or the user associated with the token)
            $user = $this->getUserFromToken($syncToken);
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }

            // Handle image upload
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . '_' . Str::slug($user->nom) . '.' . $file->getClientOriginalExtension();

                // Store in public uploads directory
                $file->move(public_path('uploads/avatar'), $filename);

                // Update user's avatar
                $user->avatar = $filename;
                $user->save();

                // Invalidate the sync token after successful update
                $this->invalidateSyncToken($syncToken);

                // Redirect back to mobile app with success
                return view('avatar-update-success', [
                    'sync_token' => $syncToken,
                    'avatar' => $filename
                ]);
            }

            return response()->json(['error' => 'Aucun fichier téléchargé'], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Mise à jour non réussie.',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Validate sync token
    private function validateSyncToken($token)
    {
        // Check if token exists and is not expired
        $tempToken = TempToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        return $tempToken !== null;
    }

    // Get user from token
    private function getUserFromToken($token)
    {
        $tempToken = TempToken::where('token', $token)->first();
        return $tempToken ? User::find($tempToken->user_id) : null;
    }

    // Invalidate sync token after use
    private function invalidateSyncToken($token)
    {
        TempToken::where('token', $token)->delete();
    }

    // Generate sync token
    public function generateSyncToken(Request $request)
    {
        $user = Auth::user();

        // Supprimer les anciens tokens
        TempToken::where('user_id', $user->id)->delete();

        // Créer un nouveau token
        $token = Str::random(60);
        $tempToken = TempToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(10)
        ]);

        return response()->json([
            'token' => $token,
            'expires_at' => $tempToken->expires_at
        ]);
    }

    public function companyUpdate($id, Request $request)
    {
        try {
            // Authentification de l'utilisateur actuel
            $user = JWTAuth::parseToken()->authenticate();
            $company_logo = \App\Models\Company::GetLogo();

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'nullable|string',
                'num_cc' => 'nullable|string',
                'num_rccm' => 'nullable|string',
                'logoBase64' => 'nullable|array',
                'logoBase64.data' => 'nullable|string',
                'logoBase64.mimeType' => 'nullable|string',
                'logoBase64.filename' => 'nullable|string',
            ]);


            // Mise à jour de l'entreprise (utilisateur)
            $user = User::where('id', $user->id)->first();
            $user->name = $request->name;
            $user->name = $request->email;
            $user->save();

            $company = Company::where('id', $user->company_id)->first();
            $company->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'tax_id' => $request->num_cc,
                'registration_number' => $request->num_rccm,
            ]);

            // Traitement de l'image en base64 si fournie
            if ($request->has('logoBase64') && !empty($request->logoBase64['data'])) {
                $imageData = $request->logoBase64['data'];
                $fileName = $company_logo . time();

                // Créer le dossier s'il n'existe pas
                $uploadPath = storage_path('uploads/logo');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Sauvegarder l'image décodée
                $imagePath = $uploadPath . '/' . $fileName;
                file_put_contents($imagePath, base64_decode($imageData));
            }

            // Récupération des valeurs actuelles pour la réponse
            $telephone = Company::where('id', $user->company_id)->select('phone')->first();
            $adresse = Company::where('id', $user->company_id)->select('address')->first();
            $numeroCC = Company::where('id', $user->company_id)->select('tax_id')->first();
            $rccm = Company::where('id', $user->company_id)->select('registration_number')->first();
            $logo = Company::where('id', $user->company_id)->select('logo')->first();
            $logoLight = Company::where('id', $user->company_id)->select('logo')->first();

            return response()->json([
                'success' => 'Mise à jour réussie.',
                'data' => [
                    'company' => $company,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'logoDark' => $user->id . '_' . $logo,
                    'logoLight' => $user->id . '_' . $logoLight,
                    'numeroCC' => $numeroCC,
                    'rccm' => $rccm,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Mise à jour non réussie.',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function MonthTraitCompany(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $salaryData = PaySlip::where('company_id', $user->company_id)->orderBy('id', 'desc')->first();
            return response()->json($salaryData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function changePasswordCompany($id, Request $request)
    {
        // Validation des données
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ]);

        try {
            // Récupération de l'utilisateur
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
            }

            // Vérification de l'ancien mot de passe
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['error' => 'Ancien mot de passe incorrect.'], 400);
            }

            // Mise à jour du mot de passe
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['success' => 'Mot de passe mis à jour avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue.', 'details' => $e->getMessage()], 500);
        }
    }

    public function companyData(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $companyData = User::where('id', $user->id)->first();
            return response()->json($companyData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.'], 401);
        }
    }

    public function profilCompany(Request $request)
    {
        try {
            // Authentification de l'utilisateur via JWT
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
            }

            // Récupération des informations de l'entreprise
            $company = Company::where('id', $user->company_id)->first();

            if (!$company) {
                return response()->json(['error' => 'Entreprise non trouvée.'], 404);
            }

            // Récupération sécurisée du secteur
            $secteurName = $company->industry; // Valeur par défaut
            if (class_exists('App\Models\Sector') && $company->industry) {
                $secteur_activite = \App\Models\Sector::where('slug', $company->industry)->first();
                if ($secteur_activite) {
                    $secteurName = $secteur_activite->name ?? $secteur_activite->nom_secteur ?? $company->industry;
                }
            }

            // Retourner les données
            return response()->json([
                'status' => true,
                'company' => $company,
                'data' => [
                    'telephone' => $company->phone,
                    'adresse' => $company->address,
                    'logoDark' => $company->logo . '?' . time(),
                    'logoLight' => $company->logo . '?' . time(),
                    'boitePostale' => $company->postal_code,
                    'grilleSalariale' => $secteurName,
                    'tauxAccidentTravail' => $company->company_state,
                    'numeroCC' => $company->tax_id,
                    'rccm' => $company->registration_number,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }

    // Récupérer les pointages pour une date spécifique
    public function getPointagesByDate($date)
    {
        try {
            // Authentification de l'utilisateur via JWT
            $user = JWTAuth::parseToken()->authenticate();

            // Vérifier si la date est au bon format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return response()->json(['success' => false, 'message' => 'Format de date invalide. Utilisez YYYY-MM-DD'], 400);
            }

            // Récupérer tous les employés de l'utilisateur
            $employees = Employee::where('company_id', $user->company_id)
                ->orderBy('name', 'asc')
                ->get();

            $formattedResults = [];

            foreach ($employees as $employee) {
                // Récupérer les pointages de cet employé pour la date donnée
                $pointages = Pointeuse::where('emp_id', $employee->id)
                    ->whereDate('auth_date_time', $date)
                    ->orderBy('auth_date_time', 'asc')
                    ->get();

                // Créer un tableau avec l'heure d'arrivée et de départ
                $attendance = [];
                foreach ($pointages as $pointage) {
                    $attendance[] = $pointage->auth_date_time;
                }

                // S'assurer qu'il y a toujours au moins 2 éléments dans le tableau
                while (count($attendance) < 2) {
                    $attendance[] = null;
                }

                $formattedResults[] = [
                    'id' => $employee->id,
                    'name_emp' => $employee->name,
                    'attendance' => $attendance
                ];
            }

            return response()->json(['success' => true, 'data' => $formattedResults], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    // Enregistrer un nouveau pointage
    public function createPointage($employee_id)
    {
        try {
            // Authentification de l'utilisateur via JWT
            $user = JWTAuth::parseToken()->authenticate();

            $employee = Employee::find($employee_id);
            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Employé non trouvé'], 404);
            }

            // Vérifier si l'employé appartient à l'utilisateur
            if ($employee->company_id != $user->id) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé à cet employé'], 403);
            }

            $now = Carbon::now();
            $currentDate = $now->toDateString();

            // L'emplacement de l'utilisateur connecté
            $location = WorkLocation::where('company_id', '=', $user->company_id)->where('is_active', '=', 1)->first();

            // Vérifier si l'employé a déjà deux pointages aujourd'hui
            $existingPointages = Pointeuse::where('emp_id', $employee_id)
                ->whereDate('auth_date_time', $currentDate)
                ->count();

            if ($existingPointages >= 2) {
                return response()->json(['success' => false, 'message' => "Deux pointages déjà enregistrés pour aujourd'hui"], 400);
            }

            // Enregistrer le pointage
            $pointage = Pointeuse::create([
                'employee_id' => $employee_id,
                'auth_date_time' => $now,
                'auth_date' => $now->toDateString(),
                'auth_time' => $now->toTimeString(),
                'type' => 'entree' ? 'entree' : 'sortie',
                'direction' => $user->name,
                'name_device' => 'Pointeuse QR CODE',
                'emp_id' => $employee_id ?? null,
                'no_device' => $user->id,
                'location_id' => $location->id ?? null,
                'card_no' => 'N/A',
                'name_emp' => $employee->name,
                'status' => 1,
                'company_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pointage enregistré avec succès',
                'timestamp' => $now->toDateTimeString()
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }

    public function getEmployeesRewards(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employees = Employee::where('employees.company_id', $user->company_id)
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->join('branches', 'employees.branch_id', '=', 'branches.id')
                ->join('designations', 'employees.designation_id', '=', 'designations.id')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->join('awards', 'employees.id', '=', 'awards.employee_id')
                ->join('award_types', 'awards.award_type', '=', 'award_types.id')
                ->select('employees.*', 'awards.id as isStarPerformer', 'users.avatar as avatar', 'award_types.name as award_type_name', 'branches.name as branch_name', 'designations.name as designation_name', 'departments.name as department_name')
                ->get();
            return response()->json(
                ['success' => true, 'data' => $employees],
                200
            );

        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.'], 401);
        }
    }

    public function createEmployeeRewards($employee_id, $awardtype_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $now = Carbon::now();
            $currentDate = $now->toDateString();

            // Get the award type
            $awardType = AwardType::find($awardtype_id);

            if (!$awardType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de récompense non trouvé'
                ], 404);
            }

            // Create the award
            $award = Award::create([
                'employee_id' => $employee_id,
                'award_type' => $awardtype_id,
                'date' => $currentDate,
                'gift' => 'N/A',
                'description' => $awardType->name, // Use the award type name
                'company_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Récompense enregistrée avec succès',
                'timestamp' => $now->toDateTimeString()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Information non disponible: ' . $e->getMessage()
            ], 401);
        }
    }

    public function getTypeRewards(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $typeRewards = AwardType::where('company_id', $user->company_id)->get();
            return response()->json(
                ['success' => true, 'data' => $typeRewards],
                200
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.'], 401);
        }
    }

    public function getCotisationCompany($date)
    {
        try {
            // Debug: Vérifier si le token est reçu
            $token = request()->bearerToken();
            \Log::info('Token reçu: ' . ($token ? 'OUI' : 'NON'));

            $user = JWTAuth::parseToken()->authenticate();
            \Log::info('User authentifié: ' . ($user ? $user->id : 'NON'));
            $formate_month_year = $date; // le format de date doit etre 2025-01
            $validatePaysilp = PaySlip::where('salary_month', '=', $formate_month_year)->where('company_id', $user->company_id)->get()->toarray();
            $data = [];
            if (empty($validatePaysilp)) {
                $data = [];
                return;
            }

            //             if (empty($validatePaysilp)) {
//     return response()->json([
//         'status' => 'success',
//         'message' => 'Aucune cotisation trouvée pour ce mois',
//         'data' => []
//     ], 200);
// }

            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    'employees.gender',
                    'employees.salary',
                    'employees.user_id',
                    'employees.tax_payer_id',
                    'employees.charge_expat',
                    'pay_slips.salary_brut',
                    'pay_slips.net_imposable',
                    'pay_slips.net_sociale',
                    'pay_slips.basic_salary',
                    'pay_slips.total_retenue',
                    'pay_slips.net_payble',
                    'pay_slips.allowance',
                    'pay_slips.Imp_brut',
                    'pay_slips.ricf',
                    'pay_slips.imp_net',
                    'pay_slips.cnps_sal',
                    'pay_slips.cnps_emp',
                    'pay_slips.taxe_fpc',
                    'pay_slips.taxe_appr',
                    'pay_slips.nbre_jour',
                    'pay_slips.ce_exp_emp',
                    'pay_slips.ce_emp',
                    'pay_slips.cmu_emp',
                    'pay_slips.cmu_sal',
                    'pay_slips.acc_trav',
                    'pay_slips.pf_emp',
                ]
            )->leftjoin(
                    'employees',
                    function ($join) use ($formate_month_year) {
                        $join->on('employees.id', '=', 'pay_slips.employee_id');
                        $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                    }
                )->where('employees.company_id', $user->company_id)->where('employees.is_active', '!=', 2)->get();

            foreach ($paylip_employee as $employee) {
                $data[] = [
                    'employee_id' => \Auth::user()->employeeIdFormat($employee->id),
                    'emp_id' => $employee->user_id,
                    'name' => $employee->name,
                    'salary_brut' => $employee->salary_brut,
                    'Imp_brut' => $employee->Imp_brut,
                    'ricf' => $employee->ricf,
                    'sexe' => $employee->gender,
                    'charge_expat' => $employee->charge_expat,
                    'imp_net' => $employee->imp_net,
                    'total_retenue' => $employee->total_retenue,
                    'nbre_jour' => $employee->nbre_jour,
                    'cnps_sal' => $employee->cnps_sal,
                    'cnps_emp' => $employee->cnps_emp,
                    'cmu_emp' => $employee->cmu_emp,
                    'cmu_sal' => $employee->cmu_sal,
                    'pf_emp' => $employee->pf_emp,
                    'acc_trav' => $employee->acc_trav,
                    'taxe_fpc' => $employee->taxe_fpc,
                    'taxe_appr' => $employee->taxe_appr,
                    'ce_exp_emp' => $employee->ce_exp_emp,
                    'ce_emp' => $employee->ce_emp,
                    'net_payble' => $employee->net_payble,
                    'net_imposable' => $employee->net_imposable,
                    'net_sociale' => $employee->net_sociale,
                ];
            }
            return $data;        //                 return response()->json([
//     'status' => 'success',
//     'data' => $data
// ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Information non disponible.'], 401);
        }
    }

    /**
     * Get notifications for a user (as creator or employee)
     */
    public function notifCompany(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $notifications = Notification::where('notifications.user_id', $user->id)
                ->join('users', 'notifications.user_id', '=', 'users.id')
                ->select('notifications.*', 'users.name as name_company')
                ->orderBy('notifications.created_at', 'desc')
                ->get();

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function notifEmployee(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $employee = Employee::where('user_id', $user->id)->first();

            $notifications = Notification::where('notifications.user_id', $employee->user_id)
                ->join('employees', 'notifications.user_id', '=', 'employees.user_id')
                ->select('notifications.*', 'employees.name as name_emp')
                ->orderBy('notifications.created_at', 'desc')
                ->get();

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->read_at = now(); // Optionnel, pour enregistrer le moment de la lecture
        $notification->save();

        return response()->json(['message' => 'Notification marquée comme lu']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsReadEmp(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $employee = Employee::where('user_id', $user->id)->first();
        $notification = Notification::where('employee_id', $employee->id)->get();
        foreach ($notification as $not) {
            $not->is_read = true;
            $not->read_at = now(); // Optionnel, pour enregistrer le moment de la lecture
            $not->save();
        }

        return response()->json(['message' => 'Toutes les notifications marquées comme lu']);
    }

    public function markAllAsReadCom(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $notification = Notification::where('user_id', $user->id)->get();
        foreach ($notification as $not) {
            $not->is_read = true;
            $not->read_at = now(); // Optionnel, pour enregistrer le moment de la lecture
            $not->save();
        }

        return response()->json(['message' => 'Toutes les notifications marquées comme lu']);
    }

    /**
     * Get count of unread notifications
     */
    public function getUnreadCountEmp()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $employee = Employee::where('user_id', $user->id)->first();
            // Initialiser la requête 
            $unreadCount = Notification::where('is_read', false)->where('user_id', $employee->user_id)->count();
            return response()->json(['unread_count' => $unreadCount]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getUnreadCountCom()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // Initialiser la requête 
            $unreadCount = Notification::where('is_read', false)->where('user_id', $user->id)->count();
            return response()->json(['unread_count' => $unreadCount]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    /**
     * Create a new notification
     */
    public function storeNotification(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'user_id' => 'required'
        ]);

        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'user_id' => $user->id,
            'is_read' => false
        ]);

        return response()->json($notification, 201);
    }

    /**
     * Enregistrer un pointage via le scan d'un QR code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scannerPointage(Request $request)
    {
        // Valider les données entrantes
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
            'type' => 'sometimes|string|in:entree,sortie', // Ajout du type optionnel
            'device_info' => 'required|array',
            'device_info.name' => 'required|string',
            'device_info.model' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer l'utilisateur authentifié
            $user = JWTAuth::parseToken()->authenticate();
            $qrData = $request->input('qr_data');
            $typeFromRequest = $request->input('type'); // Récupérer le type depuis la requête

            // Récupérer les informations de l'employé
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employé non trouvé pour cet utilisateur'
                ], 404);
            }

            $company = User::find($employee->company_id);

            // L'emplacement de l'utilisateur connecté
            $location = WorkLocation::where('company_id', '=', $user->company_id)
                ->where('is_active', '=', 1)
                ->first();

            $locationId = null;

            // Vérifier si les données du QR code sont valides
            $qrParts = explode(':', $qrData);

            if (count($qrParts) >= 3 && $qrParts[0] === 'pointage') {
                // Format: pointage:location_id:timestamp
                $locationId = $qrParts[1];
            } else if (filter_var($qrData, FILTER_VALIDATE_URL)) {
                // Format URL: traiter comme URL
                $parsedUrl = parse_url($qrData);

                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $query);
                    $locationId = $query['location_id'] ?? null;
                }

                // Si pas de location_id dans l'URL, utiliser celle de l'utilisateur (si disponible)
                if (!$locationId) {
                    $locationId = $location->id;
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Code QR invalide'
                ], 400);
            }

            // Vérifier que nous avons un location_id valide
            if (!$locationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune localisation trouvée pour ce pointage'
                ], 400);
            }

            // Déterminer le type de pointage
            $now = Carbon::now();
            $pointageType = $typeFromRequest ?: $this->determinerTypePointage($employee->id, $now);

            Pointeuse::create([
                'employee_id' => $employee->id,
                'auth_date_time' => $now,
                'auth_date' => $now->toDateString(),
                'auth_time' => $now->toTimeString(),
                'type' => $pointageType,
                'direction' => $company->name ?? 'N/A',
                'name_device' => 'Pointeuse QR CODE ' . $request->device_info['name'],
                'no_device' => $user->id,
                'emp_id' => $employee->id,
                'card_no' => 'N/A',
                'name_emp' => $employee->name,
                'location_id' => $locationId,
                'status' => 1,
                'company_id' => $employee->company_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pointage ' . ($pointageType === 'entree' ? 'entrée' : 'sortie') . ' enregistré avec succès',
                'timestamp' => $now->toDateTimeString(),
                'type' => $pointageType,
                'location_id' => $locationId,
            ], 201);

        } catch (\Exception $e) {
            // Log l'erreur pour debugging
            \Log::error('Erreur pointage QR: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'qr_data' => $qrData ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du pointage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateTemporaryToken(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate(); // Récupère l'utilisateur JWT

            // Générer un token unique
            $temporaryToken = Str::random(64);

            // Stocker le token en cache avec l'ID utilisateur (valide pendant 1 heure)
            Cache::put('temp_token_' . $temporaryToken, $user->id, 60 * 60);

            return response()->json([
                'temporaryToken' => $temporaryToken,
                'expiresIn' => 3600 // 1 heure en secondes
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la generation du token", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // Mettre à jour l'avatar
    public function updateTheAvatar(Request $request)
    {
        try {
            $user = null;

            // Vérifier si un token temporaire est fourni (dans l'URL ou dans le formulaire)
            $temporaryToken = $request->query('token') ?? $request->input('token');

            if ($temporaryToken) {
                // Récupérer l'ID utilisateur à partir du token temporaire
                $userId = Cache::get('temp_token_' . $temporaryToken);

                if ($userId) {
                    // Récupérer l'utilisateur
                    $user = User::find($userId);

                    // Supprimer le token après utilisation (usage unique)
                    Cache::forget('temp_token_' . $temporaryToken);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token temporaire invalide ou expiré'
                    ], 401);
                }
            } else {
                try {
                    // Authentification via JWT
                    $user = JWTAuth::parseToken()->authenticate();
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Non authentifié',
                        'error' => $e->getMessage()
                    ], 401);
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            // Validation de l'image
            $validator = Validator::make($request->all(), [
                'profile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Stocker l'image dans 'uploads/avatar/'
            $path = $request->file('profile')->store('uploads/avatar', 'public');

            // Mettre à jour l'avatar de l'utilisateur
            $user->avatar = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully!',
                'avatar' => $path,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour de l'avatar", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de l\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déterminer si le pointage est une entrée ou une sortie
     *
     * @param  int  $employeeId
     * @param  Carbon  $now
     * @return string
     */
    private function determinerTypePointage($employeeId, Carbon $now)
    {
        // Récupérer le dernier pointage de l'employé pour aujourd'hui
        $dernierPointage = Pointeuse::where('emp_id', $employeeId)
            ->whereDate('auth_date', $now->toDateString())
            ->latest('auth_date_time')
            ->first();

        // Si pas de pointage aujourd'hui ou dernier pointage = sortie, alors c'est une entrée
        if (!$dernierPointage || $dernierPointage->type === 'sortie') {
            return 'entree';
        }

        // Sinon c'est une sortie
        return 'sortie';
    }

    //Controller des pdf important
    public function generatePdf($id, $month)
    {
        Log::debug("[RH-FLOW-DEBUG] generatePdf appelé", ['id' => $id, 'month' => $month]);

        try {
            // 1. Authentification JWT
            $user = JWTAuth::parseToken()->authenticate();
            Log::debug("[RH-FLOW-DEBUG] Etape 1 OK - Utilisateur JWT", ['user_id' => $user->id]);

            // 2. Recherche de l'employé
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employé non trouvé.', 'id' => $id], 404);
            }
            Log::debug("[RH-FLOW-DEBUG] Etape 2 OK - Employé", ['name' => $employee->name]);

            // 3. Recherche du bulletin
            $payslip = PaySlip::where('employee_id', $id)
                ->where('salary_month', $month)
                ->where('company_id', $user->company_id)
                ->first();
            if (!$payslip) {
                return response()->json(['error' => 'Bulletin non trouvé', 'employee_id' => $id, 'month' => $month], 404);
            }
            Log::debug("[RH-FLOW-DEBUG] Etape 3 OK - Bulletin trouvé", ['net' => $payslip->net_payble]);

            // 4. Données complémentaires (sans relations complexes)
            $allowances = Allowance::where('employee_id', $id)->get();
            $retenues = Retenue::where('employee_id', $id)->get();
            $totalGains = $allowances->sum('amount') + ($employee->salary ?? 0);
            $totalRet = $retenues->where('type', '!=', 'add')->sum('amount');
            $company = Company::find($user->company_id);
            $companyName = $company ? $company->name : 'Entreprise';
            Log::debug("[RH-FLOW-DEBUG] Etape 4 OK - Données préparées");

            // 5. Génération HTML autonome (sans vue Blade externe)
            $rowsAllowances = '';
            foreach ($allowances as $a) {
                $rowsAllowances .= '<tr><td>' . htmlspecialchars($a->title ?? 'Prime') . '</td><td style="text-align:right">' . number_format($a->amount, 0, ',', ' ') . '</td><td style="text-align:right">' . number_format($a->amount, 0, ',', ' ') . '</td></tr>';
            }

            $rowsRetenues = '';
            foreach ($retenues as $r) {
                if ($r->type !== 'add') {
                    $rowsRetenues .= '<tr><td>' . htmlspecialchars($r->libelle ?? 'Retenue') . '</td><td style="text-align:right">' . number_format($r->amount, 0, ',', ' ') . '</td></tr>';
                }
            }

            $html = '<!DOCTYPE html><html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#000;}
table{width:100%;border-collapse:collapse;margin-bottom:12px;}
th,td{border:1px solid #999;padding:4px 6px;}
th{background:#f0c300;font-weight:bold;}
.center{text-align:center;} .right{text-align:right;} .bold{font-weight:bold;}
.header{background:#f0c300;padding:8px;text-align:center;}
.net{background:#e8f5e9;font-size:14px;font-weight:bold;text-align:center;padding:10px;}
</style></head><body>
<div class="header"><h2>BULLETIN DE PAIE</h2>
<p>Entreprise : ' . htmlspecialchars($companyName) . ' &nbsp;|&nbsp; Matricule : ' . htmlspecialchars($employee->employee_id ?? $id) . ' &nbsp;|&nbsp; Période : ' . htmlspecialchars($month) . '</p>
</div>
<table>
<tr><td colspan="2"><strong>Nom :</strong> ' . htmlspecialchars($employee->name) . '</td>
<td colspan="2"><strong>Poste :</strong> ' . htmlspecialchars($employee->designation->name ?? '-') . '</td></tr>
<tr><td colspan="2"><strong>Téléphone :</strong> ' . htmlspecialchars($employee->phone ?? '-') . '</td>
<td colspan="2"><strong>Date embauche :</strong> ' . htmlspecialchars($employee->company_doj ?? '-') . '</td></tr>
</table>
<table>
<thead><tr><th>Désignation</th><th>Base</th><th>Montant (FCFA)</th></tr></thead>
<tbody>
<tr><td>Salaire de base</td><td class="right">' . number_format($employee->salary ?? 0, 0, ',', ' ') . '</td><td class="right">' . number_format($employee->salary ?? 0, 0, ',', ' ') . '</td></tr>
' . $rowsAllowances . '
<tr class="bold"><td colspan="2">Total Gains</td><td class="right">' . number_format($totalGains, 0, ',', ' ') . '</td></tr>
</tbody>
</table>
<table>
<thead><tr><th>Retenues</th><th>Montant (FCFA)</th></tr></thead>
<tbody>
' . $rowsRetenues . '
<tr class="bold"><td>Total Retenues</td><td class="right">' . number_format($totalRet, 0, ',', ' ') . '</td></tr>
</tbody>
</table>
<div class="net">NET À PAYER : ' . number_format($payslip->net_payble ?? 0, 0, ',', ' ') . ' FCFA</div>
<p style="text-align:right;margin-top:40px;">Fait le ' . date('d/m/Y') . '<br><br>Signature<br>_______________</p>
</body></html>';

            Log::debug("[RH-FLOW-DEBUG] Etape 5 OK - HTML généré", ['len' => strlen($html)]);

            // 6. Génération PDF
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', false);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            Log::debug("[RH-FLOW-DEBUG] Etape 6 OK - PDF", ['size' => strlen($output)]);

            return response($output, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Bulletin_' . $employee->name . '_' . $month . '.pdf"')
                ->header('Content-Length', strlen($output));

        } catch (\Exception $e) {
            Log::error("[RH-FLOW-DEBUG] Erreur PDF", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'Erreur génération PDF',
                'details' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function generatePdfLogo($id, $monthpaie = null, $colorone = null, $colortwo = null)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true); // Activer les URL distantes

        $dompdf = new Dompdf($options);

        $secteurs = Sector::select('*')->get();
        $user = JWTAuth::parseToken()->authenticate();
        $paytype = PaymentType::where('company_id', '=', $user->company_id)->get();
        $payslip = PaySlip::where('employee_id', $id)->where('salary_month', $monthpaie)->where('company_id', $user->company_id)->first();

        if (!$payslip) {
            return response()->json(['error' => 'Bulletin non trouvé.'], 404);
        }

        $payslipss = PaySlip::where('employee_id', $id)->get();
        $logo = \App\Models\Company::get_file('uploads/logo/');
        $company_logo = Company::get_company_logo();
        $employee = Employee::find($id);
        $branche = Branch::find($employee->branch_id);
        $payslipDetail = []; // Company::employeePayslipDetail() n'existe pas - désactivé
        $avantages = Avantage::where('employee_id', $id)->get();
        $termination = Rupture::where('employee_id', '=', $id)->get();
        $contracts = Contract::where('employee_id', $employee->id)->orderby('id', 'desc')->first();
        //$loanretenue        = Loan::where('employee_id', '=', $employee->user_id)->get();
        $categorie = JobCategorie::where('id_secteur', $employee->secteur_id)->get();
        $retenues = Retenue::where('employee_id', $payslip->employee_id)->where('type', 'add')->first();
        $allowances = Allowance::where('employee_id', $id)->get();
        $colorone = '#f0c300';
        $colortwo = '#000';
        $tables = [
            'SecteurIndustrielOuvrier',
            'SecteurIndustrielEmploye',
            'SecteurIndusAgent',
            'SecteurIndusCadre',
            'SecIndusChauffeur',
            'SecteurSecuriteChauffeur',
            'SecteurSecuriteEmploye',
            'SecteurSecuriteChauffeur',
            'SecteurTrpsFondEmploye',
            'SecteurTrpsFondAgent',
            'SecteurTrpsFondCadre',
            'SecteurIndustrielBoisCadre',
            'SecteurIndustrielBoisOuvrier',
            'SecteurIndustrielBoisEmploye',
            'SecteurIndustrielBoisAgent',
            'SecteurIndustrielBoisChauffeur',
            'SecteurIndustrielTextOuvrier',
            'SecteurIndustrielTextEmploye',
            'SecteurIndustrielTextAgent',
            'SecteurIndustrielTextCadre',
            'SecteurIndustrielTextChauffeur',
            'SecteurIndustrielAgriOuvrier',
            'SecteurIndustrielAgriEmploye',
            'SecteurIndustrielAgriAgent',
            'SecteurIndustrielAgriCadre',
            'SecteurIndustrielAgriChauffeur',
            'SecteurIndustrielSucreOuvrier',
            'SecteurIndustrielSucreEmploye',
            'SecteurIndustrielSucreAgent',
            'SecteurIndustrielSucreCadre',
            'SecteurIndustrielSucreChauffeur',
            'SecteurHotelleri',
            'SecteurHotellerieMaitrise',
            'SecteurHotellerisCadre',
            'SecteurBatiment',
            'SecteurBatimentEmploye',
            'SecteurBatimentChauffeur',
            'SecteurBatimentAgent',
            'SecteurBatimentCadre',
            'SecteurDockersEmploye',
            'SecteurCommerceEmployee',
            'SecteurCommerceAgent',
            'SecteurCommerceCadre',
            'SecteurAgriCcrcOuvrier',
            'SecteurAgriCcrcEmploye',
            'SecteurAgriCcrcChauffeur',
            'SecteurAgriAutreOuvrier',
            'SecteurAgriAutreEmploye',
            'SecteurAgriAutreChauffeur',
            'SecteurElevageOuvrier',
            'SecteurElevageChauffeur',
            'SecteurElevageEmploye',
            'SecteurForestierOuvrier',
            'SecteurForestierChauffeur',
            'SecteurForestierEmploye',
            'SecteurBanqueEmploye',
            'SecteurBanqueAgent',
            'SecteurAssurancesEmploye',
            'SecteurAssurancesAgent',
            'SecteurPetroProdEmploye',
            'SecteurPetroProdChauffeur',
            'SecteurPetroProdAgent',
            'SecteurPetroProdCadre',
            'SecteurPetroDistAgent',
            'SecteurPetroDistCadre',
            'SecteurPetroDistChauffeur',
            'SecteurPetroDistEmploye',
            'SecteurMaritimePoly',
            'SecteurMaritimeMatelo',
            'SecteurMaritimeMaitre',
            'SecteurMaritimeMachine',
            'SecteurMaritimeChefMeca',
            'SecteurMaritimeSecondMeca',
            'SecteurMaritimeCapit',
            'SecteurMaritimeSecondCapit',
            'SecteurPechesNovice',
            'SecteurPechesEleve',
            'SecteurPechesSbrevet',
            'SecteurPechesBrevet',
            'SecteurPechesBosc',
            'SecteurPechesChefMoteur',
            'SecteurPechesSceonBosco',
            'SecteurPechesGraisseur',
            'SecteurPechesCapit',
            'SecteurPechesSconCapit',
            'SecteurPechesSconMeca',
            'SecteurPechesOffPon',
            'SecteurPechesLargesNovice',
            'SecteurPechesLargesEleve',
            'SecteurPechesLargesCuisto',
            'SecteurPechesLargesMatlot',
            'SecteurPechesLargesMatlotsSimple',
            'SecteurPechesLargesOffPont',
            'SecteurPechesLargesBoscoElec',
            'SecteurPechesLargesSecondBosco',
            'SecteurPechesCotiereNoviece',
            'SecteurPechesCotiereEleve',
            'SecteurPechesCotiereMatlotSimpl',
            'SecteurPechesCotiereMatlot',
            'SecteurPechesCotiereCapi',
            'SecteurPechesCotiereCapisCapa',
            'SecteurPechesCotiereMeca',
            'SecteurPechesCotiereBosco',
            'SecteurPechesCotiereSecondBoco',
            'SecteurTourism',
            'SecteurTourismsMatrise',
            'SecteurTourismsCadre',
            'SecteurTransportOuvrier',
            'SecteurTransportEmploye',
            'SecteurTransportAgent',
            'SecteurTransportCadre',
            'SecteurTrpsAerienOuvrier',
            'SecteurTrpsAerienAgent',
            'SecteurTrpsAerienCadre',
            'SecteurTrpsAerienCadresSup',
            'SecteurMaisonEmploye',
            'SecteurNettoyageOuvrier',
            'SecteurNettoyageChauffeur',
            'SecteurNettoyageEmploye',
            'SecteurIndustrielThonOuvrier',
            'SecteurIndustrielThonEmploye',
            'SecteurIndustrielThonAgent',
            'SecteurIndustrielThonCadre',
            'SecteurIndustrielThonChauffeur',
            'SecteurIndustrielPolyOuvrier',
            'SecteurIndustrielPolyEmploye',
            'SecteurIndustrielPolyCadre',
            'SecteurIndustrielPolyAgent',
            'SecteurPechesLargesGraisse',
            'SecteurPechesMecani',
            'SecteurTransportChauffeur',
        ];

        $data = [];

        foreach ($tables as $table) {
            $model = app("App\\Models\\$table");
            $result = $model->where('type_poste', $employee->sous_categorie)->get();
            // Stockez les données dans un tableau associatif
            $data[$table] = $result;
        }

        // Chargez la vue
        $html = view('payslip.bulletins.bullunlogo', compact('data', 'logo', 'company_logo', 'colortwo', 'colorone', 'termination', 'retenues', 'allowances', 'monthpaie', 'contracts', 'avantages', 'payslip', 'employee', 'payslipDetail', 'payslipss', 'branche', 'categorie', 'paytype', 'secteurs'));

        // Chargez le contenu HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Configurez le papier et l'orientation
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Envoyer le PDF au navigateur
        return $dompdf->stream('Bulletin_' . $employee->name . '_' . $monthpaie . '.pdf');
    }

    public function updateAvatar(Request $request)
    {
        // 1. Validation de l'image
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2MB
        ]);

        // 2. Récupérer l'utilisateur connecté
        $user = JWTAuth::parseToken()->authenticate();

        // 3. Stocker le fichier
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            // Générer un nom de fichier unique
            $filename = $user->id . '_' . time() . '.' . $request->avatar->extension();

            // Sauvegarder le fichier
            $path = $request->file('avatar')->storeAs(
                'public/avatars',
                $filename
            );

            // 4. Mettre à jour le champ avatar dans la base de données
            $user->avatar = $filename;
            $user->save();

            // 5. Retourner une réponse
            return response()->json([
                'message' => 'Avatar mis à jour avec succès',
                'avatar' => asset('storage/avatars/' . $filename)
            ], 200);
        }

        return response()->json(['message' => 'Aucun fichier téléchargé'],  400);
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $path = $request->file('image')->store('uploads', 'public');

        $user = User::find($request->user_id);
        $user->avatar = $path;
        $user->save();

        return response()->json(['message' => 'Upload réussi', 'path' => $path, 'avatar' => $user->avatar]);
    }

    /**
     * Méthode pour soumettre une nouvelle demande de feuille de temps
     */
    public function store(Request $request)
    {
        try {
            // Journalisation des données reçues
            Log::info('Données de la requête:', [
                'body' => $request->all(),
                'file' => $request->hasFile('justificatif') ? [
                    'originalName' => $request->file('justificatif')->getClientOriginalName(),
                    'extension' => $request->file('justificatif')->getClientOriginalExtension(),
                    'size' => $request->file('justificatif')->getSize(),
                    'mimetype' => $request->file('justificatif')->getMimeType()
                ] : null
            ]);

            // Validation des champs
            $validator = Validator::make($request->all(), [
                'category_demande' => 'required|string',
                'type_demande' => 'required|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'motif' => 'required|string',
                'justificatif' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120' // 5 Mo max
            ]);

            if ($validator->fails()) {
                Log::warning('Validation échouée', [
                    'errors' => $validator->errors(),
                    'input' => $request->all()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Gestion du fichier justificatif
            $fichierPath = null;
            if ($request->hasFile('justificatif')) {
                $fichier = $request->file('justificatif');
                $fichierPath = $fichier->store('justificatifs', 'public');
            }

            // Création de la demande de feuille de temps
            $timesheet = Timesheet::create([
                'category_demande' => $request->input('category_demande'),
                'type_demande' => $request->input('type_demande'),
                'date_debut' => $request->input('date_debut'),
                'date_fin' => $request->input('date_fin'),
                'motif' => $request->input('motif'),
                'fichier_justificatif' => $fichierPath
            ]);

            // Journalisation du succès
            Log::info('Demande soumise avec succès', [
                'timesheetId' => $timesheet->id,
                'categoryDemande' => $timesheet->category_demande
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Demande soumise avec succès',
                'data' => $timesheet
            ], 201);

        } catch (\Exception $error) {
            // Journalisation de l'erreur détaillée
            Log::error('Erreur lors de la soumission de la demande', [
                'message' => $error->getMessage(),
                'trace' => $error->getTraceAsString(),
                'file' => $error->getFile(),
                'line' => $error->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la soumission de la demande',
                'details' => $error->getMessage()
            ], 500);
        }
    }
}

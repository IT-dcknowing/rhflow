<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Employees\Models\Employee;
use App\Services\SalaryService;

$employee = Employee::where('is_active', 1)->first();
if (!$employee) {
    echo "Aucun employé trouvé.";
    exit;
}

$salaryService = new SalaryService();
// On simule pour 30 jours
$impots = $salaryService->netSalary($employee, 1); // Période 1 par défaut pour le test

echo "--- RÉSULTAT DU TEST SUR L'APP ---\n";
echo "Employé : " . $employee->name . "\n";
echo "Salaire de base : " . $employee->salary . " FCFA\n";
echo "Nombre de parts : " . $employee->parts . "\n";
echo "----------------------------------\n";
echo "Calcul avec les nouvelles règles effectué avec succès.\n";

<?php
/**
 * Test rapide du système RH Flow
 */

// Vérifier les fichiers générés
echo "=== TEST RAPIDE RH FLOW ===\n\n";

echo "1. Fichiers CSS générés : ";
echo file_exists(__DIR__ . '/css/app.css') ? "✅" : "❌";
echo "\n";

echo "2. Fichiers JS générés : ";
echo file_exists(__DIR__ . '/js/app.js') ? "✅" : "❌";
echo "\n";

echo "3. Base de données : ";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=laravel', 'root', '');
    echo "✅";
} catch (Exception $e) {
    echo "❌ (" . $e->getMessage() . ")";
}
echo "\n";

echo "4. Modules activés : ";
$modulesStatuses = json_decode(file_get_contents(__DIR__ . '/../modules_statuses.json'), true);
echo implode(', ', array_keys($modulesStatuses));
echo "\n";

echo "\n=== URLs DE TEST ===\n";
echo "• Login : http://localhost:8000/login\n";
echo "• Super Admin : http://localhost:8000/super-admin\n";
echo "• Diagnostic complet : http://localhost:8000/diagnostic.php\n";

echo "\n=== COMPTE DE TEST ===\n";
echo "Email : admin@rhflow.com\n";
echo "Mot de passe : AdminRH2024!\n";

echo "\n=== TEST TERMINÉ ===\n";
?>

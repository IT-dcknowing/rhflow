<?php
/**
 * Script d'activation du module Chatbot — À SUPPRIMER APRÈS UTILISATION
 * Accès : https://rhflow.dc-knowing.com/activate-chatbot.php?key=rhflow2024
 */

// Clé de sécurité pour éviter un accès non autorisé
$secretKey = 'rhflow2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Accès refusé.');
}

$basePath = dirname(__DIR__); // Racine du projet Laravel
$results  = [];

// ─── 1. Mettre à jour modules_statuses.json ───────────────────────────────
$statusFile = $basePath . '/modules_statuses.json';
if (file_exists($statusFile)) {
    $statuses = json_decode(file_get_contents($statusFile), true);
    $statuses['Chatbot'] = true;
    file_put_contents($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));
    $results[] = '✅ modules_statuses.json mis à jour — Chatbot activé';
} else {
    // Créer le fichier s'il n'existe pas
    $statuses = ['Chatbot' => true];
    file_put_contents($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));
    $results[] = '✅ modules_statuses.json créé avec Chatbot activé';
}

// ─── 2. Mettre à jour bootstrap/cache/modules.php ─────────────────────────
$modulesCache = $basePath . '/bootstrap/cache/modules.php';
$chatbotProvider = 'Modules\\Chatbot\\Providers\\ChatbotServiceProvider';

$cacheContent = "<?php return array (\n  'providers' => \n  array (\n";
$cacheContent .= "    0 => '{$chatbotProvider}',\n  ),\n  'eager' => \n  array (\n";
$cacheContent .= "    0 => '{$chatbotProvider}',\n  ),\n  'deferred' => \n  array (\n  ),\n);";

// Lire le cache existant et injecter Chatbot si absent
if (file_exists($modulesCache)) {
    $existing = file_get_contents($modulesCache);
    if (strpos($existing, 'ChatbotServiceProvider') === false) {
        // Injecter le provider Chatbot en premier dans providers et eager
        $existing = preg_replace(
            "/'providers'\s*=>\s*\n\s*array\s*\(\s*\n/",
            "'providers' => \n  array (\n    0 => '{$chatbotProvider}',\n",
            $existing
        );
        $existing = preg_replace(
            "/'eager'\s*=>\s*\n\s*array\s*\(\s*\n/",
            "'eager' => \n  array (\n    0 => '{$chatbotProvider}',\n",
            $existing
        );
        file_put_contents($modulesCache, $existing);
        $results[] = '✅ bootstrap/cache/modules.php mis à jour — ChatbotServiceProvider injecté';
    } else {
        $results[] = '✅ bootstrap/cache/modules.php — ChatbotServiceProvider déjà présent';
    }
} else {
    $results[] = '⚠️ bootstrap/cache/modules.php introuvable — exécutez php artisan optimize';
}

// ─── 3. Vider les caches Laravel ──────────────────────────────────────────
$cacheDirs = [
    $basePath . '/bootstrap/cache/config.php',
    $basePath . '/bootstrap/cache/routes-v7.php',
    $basePath . '/bootstrap/cache/services.php',
];
foreach ($cacheDirs as $cacheFile) {
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
        $results[] = '🗑️ Cache supprimé : ' . basename($cacheFile);
    }
}

// Vider compilés Blade
$viewCachePath = $basePath . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        unlink($file);
        $count++;
    }
    $results[] = "🗑️ {$count} vue(s) Blade compilée(s) supprimée(s)";
}

// ─── Affichage du résultat ───────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activation Chatbot — RH Flow</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 600px; margin: 60px auto; padding: 20px; }
        h1 { color: #253e87; }
        .result { background: #f0f4ff; border-left: 4px solid #253e87; padding: 12px 16px;
                  margin: 8px 0; border-radius: 4px; font-size: 14px; }
        .warning { font-size: 13px; background: #fff3cd; border-left-color: #ffc107;
                   padding: 12px 16px; margin-top: 20px; border-radius: 4px; }
        a { color: #253e87; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🤖 Activation du module Chatbot</h1>
    <?php foreach ($results as $result): ?>
        <div class="result"><?= htmlspecialchars($result) ?></div>
    <?php endforeach; ?>
    <div class="warning">
        ⚠️ <strong>Important :</strong> Supprimez ce fichier après utilisation !<br>
        Il ne doit pas rester accessible en production.<br><br>
        → <a href="https://rhflow.dc-knowing.com/company/dashboard">Tester le dashboard</a>
    </div>
</body>
</html>

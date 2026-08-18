# Test de la Queue - Étapes à suivre

## 1. Vérifier le cache des routes
```bash
php artisan route:cache
php artisan route:clear
php artisan config:clear
```

## 2. Vérifier les routes
```bash
php artisan route:list | grep bulletins
```

Vous devriez voir :
- POST   /company/declarations/bulletins/generate-queue
- GET    /company/declarations/bulletins/progress
- GET    /company/declarations/bulletins/download

## 3. Créer le dossier de stockage
```bash
mkdir -p storage/app/public/bulletins
chmod 777 storage/app/public/bulletins
```

## 4. Créer le lien symbolique (si pas déjà fait)
```bash
php artisan storage:link
```

## 5. Configurer la Queue dans .env
```
QUEUE_CONNECTION=database
```

## 6. Créer la table jobs
```bash
php artisan queue:table
php artisan migrate
```

## 7. Démarrer le worker
```bash
php artisan queue:work --queue=default --tries=3
```

## 8. Tester l'endpoint
```bash
curl -X POST http://localhost/company/declarations/bulletins/generate-queue \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN" \
  -d '{"bulletin_type": 1, "periode_id": 1, "filename": "test"}'
```

## 9. Vérifier la progression
```bash
curl http://localhost/company/declarations/bulletins/progress?bulletin_type=1
```

## Dépannage

### Erreur 404
- Vérifier que les routes sont bien chargées : `php artisan route:clear && php artisan route:cache`
- Vérifier le namespace du controller

### Job ne s'exécute pas
- Vérifier que le worker est lancé
- Vérifier la table `jobs` : `php artisan tinker` → `DB::table('jobs')->get()`
- Vérifier les logs : `tail -f storage/logs/laravel.log`

### Progression bloquée
- Vérifier le cache : `php artisan tinker` → `cache()->get('bulletin_generation_...')`
- Vérifier que le Job a accès à la classe `GenerateBulkBulletinsPDF`

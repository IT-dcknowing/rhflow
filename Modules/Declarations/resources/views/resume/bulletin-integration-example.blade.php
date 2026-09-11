{{-- Exemple d'intégration de la Queue dans bulletins_all.blade.php --}}

{{-- Ajouter dans la section @push('scripts') --}}

@push('scripts')
    <script src="{{ asset('js/bulletin-queue.js') }}"></script>
    
    <script>
        /**
         * Déterminer automatiquement si utiliser client-side ou queue
         * Basé sur le nombre de bulletins
         */
        function downloadBulletinsAuto(bulletinType, periodeId, filename) {
            const bulletinCount = document.querySelectorAll('.pagebulletin' + bulletinType).length;
            
            console.log(`Bulletins type ${bulletinType}: ${bulletinCount}`);
            
            // Seuil : utiliser queue si > 50 bulletins
            if (bulletinCount > 50) {
                console.log('Utilisation de la Queue pour gros volume');
                generateBulletinsQueueType(bulletinType, periodeId, filename);
            } else {
                console.log('Utilisation du client-side pour petit volume');
                downloadAllBulletinsType(bulletinType, filename);
            }
        }
        
        /**
         * Wrapper pour les fonctions de queue
         */
        function generateBulletinsQueueType(bulletinType, periodeId, filename) {
            switch(bulletinType) {
                case 1:
                    generateBulletinsQueueType1(periodeId, filename);
                    break;
                case 2:
                    generateBulletinsQueueType2(periodeId, filename);
                    break;
                case 3:
                    generateBulletinsQueueType3(periodeId, filename);
                    break;
            }
        }
        
        /**
         * Wrapper pour les fonctions client-side
         */
        function downloadAllBulletinsType(bulletinType, filename) {
            switch(bulletinType) {
                case 1:
                    downloadAllBulletinsType1('mainDownloadButton', filename);
                    break;
                case 2:
                    downloadAllBulletinsType2('mainDownloadButton2', filename);
                    break;
                case 3:
                    downloadAllBulletinsType3('mainDownloadButton3', filename);
                    break;
            }
        }
    </script>
@endpush

{{-- Remplacer les boutons de téléchargement existants par : --}}

{{-- Pour Bulletin 1 --}}
<button class="btn btn-sm btn-primary" id="mainDownloadButton" 
    onclick="downloadBulletinsAuto(1, {{ $periode->id }}, 'bulletin_v1_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->translatedFormat('F Y') }}')">
    <span class="fa fa-download me-1"></span> Télécharger
</button>

{{-- Pour Bulletin 2 --}}
<button class="btn btn-sm btn-primary" id="mainDownloadButton2" 
    onclick="downloadBulletinsAuto(2, {{ $periode->id }}, 'bulletin_v2_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->translatedFormat('F Y') }}')">
    <span class="fa fa-download me-1"></span> Télécharger
</button>

{{-- Pour Bulletin 3 --}}
<button class="btn btn-sm btn-primary" id="mainDownloadButton3" 
    onclick="downloadBulletinsAuto(3, {{ $periode->id }}, 'bulletin_v3_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->translatedFormat('F Y') }}')">
    <span class="fa fa-download me-1"></span> Télécharger
</button>

{{-- 
    RÉSUMÉ DE L'INTÉGRATION :
    
    1. Inclure le script bulletin-queue.js
    2. Remplacer les onclick des boutons par downloadBulletinsAuto()
    3. La fonction détecte automatiquement :
       - < 50 bulletins : utilise client-side (rapide)
       - > 50 bulletins : utilise queue (scalable)
    4. L'utilisateur voit une barre de progression
    5. Le PDF est téléchargé automatiquement à la fin
    
    AVANTAGES :
    - Transparent pour l'utilisateur
    - Pas de timeout serveur
    - Scalable jusqu'à 10000+ bulletins
    - Notifications en temps réel
    - Gestion des erreurs robuste
--}}

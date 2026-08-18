/**
 * Gestion de la génération de bulletins en queue
 * Avec polling pour la progression et notifications
 */

class BulletinQueueManager {
    constructor() {
        this.pollingInterval = null;
        this.currentBulletinType = null;
        this.isProcessing = false;
    }

    /**
     * Déclencher la génération de bulletins en queue
     */
    async startGeneration(bulletinType, periodeId, filename) {
        if (this.isProcessing) {
            Swal.fire({
                icon: 'warning',
                title: 'En cours',
                text: 'Une génération est déjà en cours'
            });
            return;
        }

        try {
            this.currentBulletinType = bulletinType;
            this.isProcessing = true;

            // Afficher modal de progression
            this.showProgressModal(bulletinType);

            // Lancer la requête via fetch pour plus de fiabilité
            const response = await fetch('/company/declarations/resume/generate-queue', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bulletin_type: bulletinType,
                    periode_id: periodeId,
                    filename: filename
                })
            });

            if (response.status === 405) {
                throw new Error("Méthode Non Autorisée (405). Vérifiez la configuration des routes.");
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Erreur lors du lancement de la génération");
            }

            // Démarrer le polling
            this.startPolling(bulletinType);

        } catch (error) {
            console.error('Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message
            });
            this.isProcessing = false;
        }
    }

    /**
     * Démarrer le polling de progression
     */
    startPolling(bulletinType) {
        // Vérifier la progression toutes les 2 secondes
        this.pollingInterval = setInterval(async () => {
            try {
                const response = await fetch(
                    `/company/declarations/resume/progress?bulletin_type=${bulletinType}`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }
                );

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Polling error response:', text);
                    throw new Error(`Erreur serveur (${response.status})`);
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error('Non-JSON response received:', text);
                    throw new Error("Réponse non-JSON reçue du serveur");
                }

                const progress = await response.json();
                
                // Mettre à jour la barre de progression
                this.updateProgressBar(progress);

                // Vérifier si terminé
                if (progress.status === 'success') {
                    clearInterval(this.pollingInterval);
                    this.handleSuccess(progress, bulletinType);
                } else if (progress.status === 'error') {
                    clearInterval(this.pollingInterval);
                    this.handleError(progress);
                }

            } catch (error) {
                console.error('Erreur polling:', error);
                // On n'arrête pas le polling au premier échec réseau/parsing
                // sauf si c'est une erreur critique
            }
        }, 2000);
    }

    /**
     * Afficher le modal de progression
     */
    showProgressModal(bulletinType) {
        const html = `
            <div class="progress" style="height: 25px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    0%
                </div>
            </div>
            <p id="progressMessage" class="mt-3 text-center">En attente du serveur...</p>
            <div id="progressDetails" class="mt-2 text-muted small"></div>
        `;

        Swal.fire({
            title: `Génération Bulletin ${bulletinType}`,
            html: html,
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    /**
     * Mettre à jour la barre de progression
     */
    updateProgressBar(progress) {
        const progressBar = document.querySelector('#progressBar');
        const progressMessage = document.querySelector('#progressMessage');
        const progressDetails = document.querySelector('#progressDetails');

        if (progressBar) {
            progressBar.style.width = (progress.progress || 0) + '%';
            progressBar.textContent = (progress.progress || 0) + '%';
        }

        if (progressMessage) {
            progressMessage.textContent = progress.message || 'Traitement...';
        }

        if (progressDetails) {
            progressDetails.textContent = `Statut: ${progress.status || 'inconnu'}`;
        }
    }

    /**
     * Gérer le succès
     */
    handleSuccess(progress, bulletinType) {
        this.isProcessing = false;

        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: progress.message,
            confirmButtonText: 'Télécharger'
        }).then((result) => {
            if (result.isConfirmed) {
                this.downloadBulletin(progress.file_path);
            }
        });
    }

    /**
     * Gérer l'erreur
     */
    handleError(progress) {
        this.isProcessing = false;

        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: progress.message || 'Une erreur est survenue pendant la génération'
        });
    }

    /**
     * Télécharger le bulletin
     */
    downloadBulletin(filePath) {
        if (!filePath) return;
        const filename = filePath.split('/').pop();
        window.location.href = `/company/declarations/resume/download?filename=${filename.replace('.pdf', '')}`;
    }
}

// Instance globale
const bulletinQueue = new BulletinQueueManager();

/**
 * Fonctions pour déclencher la génération
 */
function generateBulletinsQueueType1(periodeId, filename) {
    bulletinQueue.startGeneration(1, periodeId, filename);
}

function generateBulletinsQueueType2(periodeId, filename) {
    bulletinQueue.startGeneration(2, periodeId, filename);
}

function generateBulletinsQueueType3(periodeId, filename) {
    bulletinQueue.startGeneration(3, periodeId, filename);
}
# 🤖 Guide d'Implémentation : Assistant IA RH Flow

Ce document résume l'architecture et le fonctionnement du module de Chatbot IA intégré à RH Flow.

---

## 🛠️ Configuration Technique

### 1. Intelligence Artificielle (Gemini PRO 2.0)
L'assistant utilise l'API Google Gemini 2.0 Flash. La configuration se trouve dans votre fichier `.env` :
- `GEMINI_API_KEY` : Votre clé API Google Cloud.
- `CHATBOT_DEMO` : À mettre sur `false` pour l'IA réelle, ou `true` pour une simulation.

### 2. Zéro Base de Données (Stateless)
Pour garantir la légèreté et la confidentialité :
- **Aucune table SQL n'est utilisée**.
- L'historique des discussions est sauvegardé dans le **LocalStorage** de votre navigateur.
- Si vous videz le cache de votre navigateur, la discussion est réinitialisée.

---

## 🚀 Fonctionnalités Clés

### 📝 Collecte des données de paie
L'IA est programmée pour vous aider à noter les changements du mois :
- Recrutements (Ex: "Nouveau salarié : Jean Dupont")
- Absences et congés
- Primes et augmentations
- Départs et fins de contrat

### 📄 Génération de Rapport (Simulé)
Un bouton **"Rapport"** apparaît dans la fenêtre de chat après quelques échanges. Il permet d'extraire automatiquement un tableau récapitulatif des informations données durant la discussion.

---

## 📁 Structure du Module
Les fichiers principaux se trouvent dans :
- `Modules/Chatbot/Services/GeminiService.php` (Connexion Google)
- `Modules/Chatbot/Services/PayrollChatbot.php` (Logique métier)
- `Modules/Chatbot/resources/views/widget.blade.php` (Interface utilisateur)

---
*Rapport généré le 09 Avril 2026 par Antigravity AI.*

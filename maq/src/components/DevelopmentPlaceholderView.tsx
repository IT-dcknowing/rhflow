import React from 'react';
import { motion } from 'motion/react';
import {
  Gift,
  HandCoins,
  CalendarDays,
  Clock,
  ArrowLeft,
  Calculator,
  Hammer,
  Sparkles,
  CheckCircle2,
} from 'lucide-react';
import { SidebarTabId } from './NavigationSidebar';

interface DevelopmentPlaceholderViewProps {
  tabId: SidebarTabId;
  onNavigateHome: () => void;
  onNavigateProcessing: () => void;
}

const TAB_DETAILS: Record<
  string,
  {
    title: string;
    icon: React.ComponentType<{ className?: string }>;
    description: string;
    features: string[];
  }
> = {
  primes: {
    title: 'Primes & Avantages',
    icon: Gift,
    description:
      'Module de gestion centralisée des primes contractuelles, exceptionnelles et avantages en nature selon la réglementation ivoirienne.',
    features: [
      'Prime de transport légale (exonération ITS jusqu’à 30 000 FCFA)',
      'Primes de rendement, de panier, de salissure et de technicité',
      'Avantages en nature (logement, véhicule, domesticité selon barème fiscal)',
      'Calcul automatique de l’imposabilité ITS et de l’assiette CNPS',
    ],
  },
  prets: {
    title: 'Prêts & Retenues',
    icon: HandCoins,
    description:
      'Suivi des avances sur salaire, prêts de l’employeur, acomptes et retenues judiciaires avec respect de la quotité cessible légale.',
    features: [
      'Échéanciers d’amortissement des prêts salariés',
      'Contrôle automatique de la quotité cessible (protection du salarié)',
      'Acomptes de quinzaine et avances sur salaire ponctuelles',
      'Historique des prélèvements et solde restant dû',
    ],
  },
  conges: {
    title: 'Congés & Repos',
    icon: CalendarDays,
    description:
      'Gestion des droits à congés payés (2,2 jours ouvrables par mois effectif en Côte d’Ivoire), repos hebdomadaires et congés exceptionnels.',
    features: [
      'Compteur de congés acquis (2,2 j/mois dès 1 an de présence)',
      'Majoration pour ancienneté (jusqu’à 6 jours supplémentaires)',
      'Congés exceptionnels pour événements familiaux (mariage, naissance, décès)',
      'Calcul de l’indemnité de congé payé et des retenues pour absence',
    ],
  },
  heures_sup: {
    title: 'Heure supplémentaires',
    icon: Clock,
    description:
      'Saisie et valorisation des heures supplémentaires effectuées au-delà de la durée légale de 40 heures hebdomadaires en Côte d’Ivoire.',
    features: [
      'Majorations de jour : 15% (41e à 46e heure) et 50% (au-delà de la 46e heure)',
      'Majorations de nuit : 75% en semaine et 100% dimanches et jours fériés',
      'Intégration directe dans le brut imposable et social',
      'Import pointeuse biométrique et validation des fiches de pointage',
    ],
  },
};

export const DevelopmentPlaceholderView: React.FC<DevelopmentPlaceholderViewProps> = ({
  tabId,
  onNavigateHome,
  onNavigateProcessing,
}) => {
  const details = TAB_DETAILS[tabId] || {
    title: 'Module',
    icon: Hammer,
    description: 'Ce module est en cours de conception.',
    features: ['Fonctionnalités avancées en cours d’implémentation'],
  };

  const Icon = details.icon;

  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -8 }}
      transition={{ duration: 0.25 }}
      className="flex-1 flex flex-col items-center justify-center p-8 bg-white border border-[#E8E8E6] rounded-2xl min-h-[520px] text-center"
    >
      <div className="max-w-xl w-full flex flex-col items-center gap-6">
        {/* Badge module en développement */}
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-[#1c357d] text-xs font-bold">
          <Hammer className="w-3.5 h-3.5 text-[#1c357d]" />
          <span>Page en cours de développement</span>
        </div>

        {/* Icône & Titre */}
        <div className="flex flex-col items-center gap-2">
          <div className="w-16 h-16 rounded-2xl bg-[#1c357d] text-white flex items-center justify-center shadow-md">
            <Icon className="w-8 h-8 text-white" />
          </div>
          <h2 className="text-2xl font-bold text-[#1F1F1E] tracking-tight">{details.title}</h2>
          <p className="text-xs text-[#6B6B6B] max-w-md font-medium leading-relaxed">
            {details.description}
          </p>
        </div>

        {/* Aperçu des fonctionnalités prévues */}
        <div className="w-full bg-[#FAFAFA] border border-[#E8E8E6] rounded-xl p-5 text-left">
          <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#1c357d] mb-3">
            <Sparkles className="w-3.5 h-3.5" />
            <span>Fonctionnalités au programme</span>
          </div>
          <ul className="space-y-2 text-xs text-[#1F1F1E]">
            {details.features.map((feature, idx) => (
              <li key={idx} className="flex items-start gap-2">
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 mt-0.5 shrink-0" />
                <span>{feature}</span>
              </li>
            ))}
          </ul>
        </div>

        {/* Boutons d'action */}
        <div className="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
          <button
            type="button"
            onClick={onNavigateHome}
            className="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-xs"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Retour à l'accueil Paie</span>
          </button>

          <button
            type="button"
            onClick={onNavigateProcessing}
            className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1c357d] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-md"
          >
            <Calculator className="w-3.5 h-3.5" />
            <span>Accéder au Traitement de paie</span>
          </button>
        </div>
      </div>
    </motion.div>
  );
};

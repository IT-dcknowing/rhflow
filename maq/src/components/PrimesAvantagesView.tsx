import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  Copy,
  Plus,
  ArrowLeft,
  Calculator,
  Gift,
  CheckCircle2,
  X,
  Sparkles,
  Info,
} from 'lucide-react';
import { Period } from '../data/employeeTypes';

interface PrimesAvantagesViewProps {
  currentPeriod: Period;
  onNavigateHome: () => void;
  onNavigateProcessing: () => void;
}

interface PrimeConfig {
  code: string;
  libelle: string;
  type: 'Conventionnel' | 'Libre';
  regimeSocial: 'Soumis' | 'Exonéré';
  regimeFiscal: string;
  montant: number;
  badge?: string;
}

interface AvantageNature {
  id: number;
  salarie: string;
  typeAvantage: string;
  valeurReelle: string;
  valeurBareme: string;
  statut: string;
}

export const PrimesAvantagesView: React.FC<PrimesAvantagesViewProps> = ({
  currentPeriod,
  onNavigateHome,
  onNavigateProcessing,
}) => {
  // Tableau 1 - Configuration des primes avec montants éditables
  const [primes, setPrimes] = useState<PrimeConfig[]>([
    {
      code: '66110',
      libelle: 'Sursalaire',
      type: 'Conventionnel',
      regimeSocial: 'Soumis',
      regimeFiscal: 'Exo 0 %',
      montant: 210158,
    },
    {
      code: '6638',
      libelle: 'Prime de transport légale',
      type: 'Conventionnel',
      regimeSocial: 'Exonéré',
      regimeFiscal: 'Exo 100 %',
      montant: 30000,
      badge: 'Plafond Abidjan : 30 000 F',
    },
    {
      code: '66120',
      libelle: "Prime d'ancienneté",
      type: 'Conventionnel',
      regimeSocial: 'Soumis',
      regimeFiscal: 'Exo 0 %',
      montant: 27710,
    },
    {
      code: '66130',
      libelle: 'Prime de panier',
      type: 'Libre',
      regimeSocial: 'Soumis',
      regimeFiscal: 'Exo 0 %',
      montant: 15000,
    },
    {
      code: '66140',
      libelle: 'Prime de représentation',
      type: 'Conventionnel',
      regimeSocial: 'Soumis',
      regimeFiscal: 'Exo 10 %',
      montant: 50000,
    },
  ]);

  // Tableau 2 - Avantages en nature & en argent (6 salariés)
  const [avantages, setAvantages] = useState<AvantageNature[]>([
    {
      id: 4,
      salarie: 'Diallo Ibrahim',
      typeAvantage: 'Véhicule',
      valeurReelle: '200\u00A0000',
      valeurBareme: '150\u00A0000',
      statut: 'Actif',
    },
    {
      id: 5,
      salarie: 'Fatou Koné',
      typeAvantage: 'Logement',
      valeurReelle: '300\u00A0000',
      valeurBareme: '250\u00A0000',
      statut: 'Actif',
    },
    {
      id: 1,
      salarie: 'Aka Koffi',
      typeAvantage: '–',
      valeurReelle: '–',
      valeurBareme: '–',
      statut: '–',
    },
    {
      id: 2,
      salarie: 'Bamba Aminata',
      typeAvantage: '–',
      valeurReelle: '–',
      valeurBareme: '–',
      statut: '–',
    },
    {
      id: 3,
      salarie: 'Kouamé Yao',
      typeAvantage: '–',
      valeurReelle: '–',
      valeurBareme: '–',
      statut: '–',
    },
    {
      id: 6,
      salarie: 'Axel ROAD',
      typeAvantage: '–',
      valeurReelle: '–',
      valeurBareme: '–',
      statut: '–',
    },
  ]);

  // Modal d'ajout d'élément du brut
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedElement, setSelectedElement] = useState('66150');
  const [newLibelle, setNewLibelle] = useState('Prime de salissure');
  const [newMontant, setNewMontant] = useState('25 000');
  const [notification, setNotification] = useState<string | null>(null);

  const formatNumber = (num: number) => {
    return num.toLocaleString('fr-FR').replace(/\s/g, '\u00A0');
  };

  const handleMontantChange = (index: number, valStr: string) => {
    const raw = parseInt(valStr.replace(/\D/g, ''), 10) || 0;
    const updated = [...primes];
    updated[index].montant = raw;
    setPrimes(updated);
  };

  const handleCopyPreviousMonth = () => {
    setNotification('Primes du mois précédent recopiées avec succès pour JUIN 2026.');
    setTimeout(() => setNotification(null), 3000);
  };

  const handleAddElement = (e: React.FormEvent) => {
    e.preventDefault();
    const raw = parseInt(newMontant.replace(/\D/g, ''), 10) || 0;
    const newPrime: PrimeConfig = {
      code: selectedElement,
      libelle: newLibelle,
      type: 'Libre',
      regimeSocial: 'Soumis',
      regimeFiscal: 'Exo 0 %',
      montant: raw,
    };
    setPrimes([...primes, newPrime]);
    setIsModalOpen(false);
    setNotification(`Élément « ${newLibelle} » ajouté au paramétrage.`);
    setTimeout(() => setNotification(null), 3000);
  };

  const handleAvantageTypeChange = (id: number, newType: string) => {
    setAvantages(
      avantages.map((a) => {
        if (a.id === id) {
          if (newType === '–') {
            return { ...a, typeAvantage: '–', valeurReelle: '–', valeurBareme: '–', statut: '–' };
          }
          const defaultReelle = newType === 'Véhicule' ? '200\u00A0000' : newType === 'Logement' ? '300\u00A0000' : '50\u00A0000';
          const defaultBareme = newType === 'Véhicule' ? '150\u00A0000' : newType === 'Logement' ? '250\u00A0000' : '40\u00A0000';
          return {
            ...a,
            typeAvantage: newType,
            valeurReelle: defaultReelle,
            valeurBareme: defaultBareme,
            statut: 'Actif',
          };
        }
        return a;
      })
    );
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 8 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -8 }}
      transition={{ duration: 0.22 }}
      className="flex-1 flex flex-col gap-6"
    >
      {/* Toast de confirmation */}
      {notification && (
        <div className="bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs">
          <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
          <span>{notification}</span>
        </div>
      )}

      {/* BARRE D'OUTILS (HAUT DE PAGE) */}
      <div className="bg-white border border-[#E8E8E6] rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div className="flex items-center gap-2">
          <div className="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0">
            <Gift className="w-5 h-5" />
          </div>
          <div>
            <h2 className="text-sm font-bold text-[#1F1F1E]">
              Gestion des Primes & Avantages — {currentPeriod.name || 'JUIN 2026'}
            </h2>
            <p className="text-xs text-[#6B6B6B]">
              Paramétrage des rubriques conventionnelles et avantages en nature pour les 6 salariés
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2.5">
          {/* Bouton secondaire : Recopier les primes du mois précédent */}
          <button
            type="button"
            onClick={handleCopyPreviousMonth}
            className="px-3.5 py-2 rounded-xl border border-[#D0D0CE] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
          >
            <Copy className="w-3.5 h-3.5 text-[#6B6B6B]" />
            <span>Recopier les primes du mois précédent</span>
          </button>

          {/* Bouton principal : + Ajouter un élément du brut */}
          <button
            type="button"
            onClick={() => setIsModalOpen(true)}
            className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-sm"
          >
            <Plus className="w-4 h-4" />
            <span>+ Ajouter un élément du brut</span>
          </button>
        </div>
      </div>

      {/* TABLEAU 1 – CONFIGURATION DES PRIMES */}
      <section
        id="section-primes-config"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
            Tableau 1 – Configuration des primes
          </h3>
          <span className="text-[11px] text-[#6B6B6B]">
            {primes.length} rubriques paramétrées
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">CODE</th>
                <th className="py-3 px-4">LIBELLÉ</th>
                <th className="py-3 px-4">TYPE</th>
                <th className="py-3 px-4">RÉGIME SOCIAL</th>
                <th className="py-3 px-4">RÉGIME FISCAL</th>
                <th className="py-3 px-4 text-right">MONTANT (FCFA)</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {primes.map((prime, idx) => (
                <tr key={prime.code + idx} className="hover:bg-blue-50/20 transition-colors">
                  {/* CODE */}
                  <td className="py-3.5 px-4 font-mono font-bold text-[#1e3a8a]">
                    {prime.code}
                  </td>

                  {/* LIBELLÉ + BADGE ÉVENTUEL */}
                  <td className="py-3.5 px-4 font-medium text-[#1F1F1E]">
                    <div className="flex items-center gap-2">
                      <span>{prime.libelle}</span>
                      {prime.badge && (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                          {prime.badge}
                        </span>
                      )}
                    </div>
                  </td>

                  {/* TYPE */}
                  <td className="py-3.5 px-4 text-[#6B6B6B]">
                    <span className="px-2 py-0.5 rounded-md bg-neutral-100 text-[#333] text-[11px] font-medium">
                      {prime.type}
                    </span>
                  </td>

                  {/* RÉGIME SOCIAL */}
                  <td className="py-3.5 px-4">
                    {prime.regimeSocial === 'Soumis' ? (
                      <span className="text-blue-700 font-semibold">Soumis CNPS</span>
                    ) : (
                      <span className="text-emerald-700 font-semibold">Exonéré CNPS</span>
                    )}
                  </td>

                  {/* RÉGIME FISCAL */}
                  <td className="py-3.5 px-4 text-[#1F1F1E] font-medium">
                    {prime.regimeFiscal}
                  </td>

                  {/* MONTANT (FCFA) ÉDITABLE */}
                  <td className="py-3.5 px-4 text-right">
                    <div className="inline-flex items-center justify-end">
                      <input
                        type="text"
                        value={formatNumber(prime.montant)}
                        onChange={(e) => handleMontantChange(idx, e.target.value)}
                        className="w-32 py-1 px-2 text-right font-montserrat font-bold text-xs rounded-lg border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40 focus:border-[#1e3a8a]"
                      />
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* TABLEAU 2 – AVANTAGES EN NATURE & EN ARGENT (6 SALARIÉS) */}
      <section
        id="section-avantages-nature"
        className="bg-white border border-[#E8E8E6] rounded-2xl shadow-xs overflow-hidden"
      >
        <div className="p-4 border-b border-[#E8E8E6] bg-[#F9FAFB] flex items-center justify-between">
          <h3 className="text-xs font-bold text-[#1F1F1E] uppercase tracking-wider">
            Tableau 2 – Avantages en nature & en argent (6 salariés)
          </h3>
          <span className="text-[11px] text-[#6B6B6B]">
            Évaluation fiscale forfaitaire selon le barème CI
          </span>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead>
              <tr className="border-b border-[#E8E8E6] text-[#6B6B6B] font-semibold bg-[#F9FAFB]">
                <th className="py-3 px-4">SALARIÉ</th>
                <th className="py-3 px-4">TYPE D'AVANTAGE</th>
                <th className="py-3 px-4 text-right">VALEUR RÉELLE (FCFA)</th>
                <th className="py-3 px-4 text-right">VALEUR BARÈME (FCFA)</th>
                <th className="py-3 px-4 text-center">STATUT</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              {avantages.map((item) => (
                <tr key={item.id} className="hover:bg-blue-50/20 transition-colors">
                  {/* SALARIÉ */}
                  <td className="py-3.5 px-4 font-bold text-[#1F1F1E]">
                    {item.salarie}
                  </td>

                  {/* TYPE D'AVANTAGE (MENU DÉROULANT) */}
                  <td className="py-3.5 px-4">
                    <select
                      value={item.typeAvantage}
                      onChange={(e) => handleAvantageTypeChange(item.id, e.target.value)}
                      className="py-1 px-2.5 rounded-lg border border-[#D0D0CE] bg-white text-xs font-medium text-[#1F1F1E] focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    >
                      <option value="–">– Aucun –</option>
                      <option value="Véhicule">Véhicule</option>
                      <option value="Logement">Logement</option>
                      <option value="Nourriture">Nourriture</option>
                      <option value="Téléphone">Téléphone</option>
                    </select>
                  </td>

                  {/* VALEUR RÉELLE */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-medium text-[#1F1F1E]">
                    {item.valeurReelle !== '–' ? `${item.valeurReelle}\u00A0FCFA` : '–'}
                  </td>

                  {/* VALEUR BARÈME */}
                  <td className="py-3.5 px-4 text-right font-montserrat font-bold text-[#1e3a8a]">
                    {item.valeurBareme !== '–' ? `${item.valeurBareme}\u00A0FCFA` : '–'}
                  </td>

                  {/* STATUT */}
                  <td className="py-3.5 px-4 text-center">
                    {item.statut === 'Actif' ? (
                      <span className="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                        <span>Actif</span>
                      </span>
                    ) : (
                      <span className="text-[#888] font-mono">–</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {/* BOUTONS BAS DE PAGE (Retour accueil + Accéder au traitement) */}
      <div className="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 pb-4">
        <button
          type="button"
          onClick={onNavigateHome}
          className="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-[#E8E8E6] bg-white text-[#1F1F1E] hover:bg-[#F4F4F5] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-xs"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Retour à l'accueil Paie</span>
        </button>

        <button
          type="button"
          onClick={onNavigateProcessing}
          className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:shadow-md"
        >
          <Calculator className="w-4 h-4" />
          <span>Accéder au Traitement de paie</span>
        </button>
      </div>

      {/* MODALE – AJOUTER UN ÉLÉMENT DU BRUT */}
      <AnimatePresence>
        {isModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-[#E8E8E6]"
            >
              <div className="bg-[#1e3a8a] text-white px-6 py-4 flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Plus className="w-5 h-5" />
                  <h3 className="font-bold text-sm">Ajouter un élément du brut</h3>
                </div>
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>

              <form onSubmit={handleAddElement} className="p-6 space-y-4 text-xs">
                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1.5">
                    Sélectionner la prime disponible
                  </label>
                  <div className="space-y-2 border border-[#E8E8E6] rounded-xl p-3 bg-[#FAFAFA]">
                    {[
                      { code: '66150', label: 'Prime de salissure (Conventionnel)' },
                      { code: '66160', label: 'Prime de technicité (Conventionnel)' },
                      { code: '66170', label: 'Prime de caisse (Libre)' },
                      { code: '66180', label: 'Gratification exceptionnelle (Libre)' },
                    ].map((item) => (
                      <label key={item.code} className="flex items-center gap-2.5 cursor-pointer">
                        <input
                          type="radio"
                          name="elementChoice"
                          value={item.code}
                          checked={selectedElement === item.code}
                          onChange={() => {
                            setSelectedElement(item.code);
                            setNewLibelle(item.label.split(' (')[0]);
                          }}
                          className="accent-[#1e3a8a]"
                        />
                        <span className="font-medium text-[#1F1F1E]">
                          {item.code} — {item.label}
                        </span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Libellé personnalisé
                  </label>
                  <input
                    type="text"
                    value={newLibelle}
                    onChange={(e) => setNewLibelle(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    required
                  />
                </div>

                <div>
                  <label className="block font-bold text-[#1F1F1E] mb-1">
                    Montant de référence (FCFA)
                  </label>
                  <input
                    type="text"
                    value={newMontant}
                    onChange={(e) => setNewMontant(e.target.value)}
                    className="w-full px-3 py-2 rounded-xl border border-[#D0D0CE] bg-white font-montserrat font-bold focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/40"
                    placeholder="ex: 25 000"
                    required
                  />
                </div>

                <div className="pt-3 border-t border-[#E8E8E6] flex items-center justify-end gap-2.5">
                  <button
                    type="button"
                    onClick={() => setIsModalOpen(false)}
                    className="px-4 py-2 rounded-xl border border-[#D0D0CE] text-[#1F1F1E] hover:bg-neutral-100 font-semibold cursor-pointer"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-xl bg-[#1e3a8a] text-white hover:bg-[#162a63] font-bold cursor-pointer shadow-xs"
                  >
                    Ajouter
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};

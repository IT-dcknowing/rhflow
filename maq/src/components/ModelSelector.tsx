import React from 'react';
import { ViewModelId } from '../data/employeeTypes';
import { Layers, Plus, Sparkles, FileText, GitCommitHorizontal, Table } from 'lucide-react';

interface ModelSelectorProps {
  activeModel: ViewModelId;
  onSelectModel: (model: ViewModelId) => void;
  onAddNewModel?: () => void;
}

interface ModelOption {
  id: ViewModelId;
  label: string;
  badge: string;
  subtitle: string;
  icon: React.ComponentType<{ className?: string }>;
}

const MODEL_OPTIONS: ModelOption[] = [
  {
    id: 'model1',
    label: 'Modèle 1',
    badge: 'Vue Hybride',
    subtitle: 'Masse & Tiroir Chirurgical',
    icon: Table,
  },
  {
    id: 'model2',
    label: 'Modèle 2',
    badge: 'Bulletin Vivant',
    subtitle: 'Édition directe & Split-view',
    icon: FileText,
  },
  {
    id: 'model3',
    label: 'Modèle 3',
    badge: 'Le Tunnel',
    subtitle: 'Workflow guidé en 5 étapes',
    icon: GitCommitHorizontal,
  },
];

export const ModelSelector: React.FC<ModelSelectorProps> = ({
  activeModel,
  onSelectModel,
  onAddNewModel,
}) => {
  return (
    <div
      id="model-selector-bar"
      className="bg-[#0a1f44] border-b border-white/10 px-6 py-2.5 flex items-center justify-between text-[13px]"
    >
      <div className="flex items-center gap-3 flex-wrap">
        <div className="flex items-center gap-2 text-white/90 font-bold uppercase tracking-wider text-[12px] pr-2 border-r border-white/15">
          <Layers className="w-4 h-4 text-[#3b82f6]" />
          <span>Affichage RH :</span>
        </div>

        {/* Buttons [ Modèle 1 ]  [ Modèle 2 ]  [ Modèle 3 ] */}
        <div className="inline-flex items-center bg-white/10 p-1 rounded-xl gap-1.5">
          {MODEL_OPTIONS.map((opt) => {
            const isActive = activeModel === opt.id;
            const Icon = opt.icon;
            return (
              <button
                key={opt.id}
                id={`model-btn-${opt.id}`}
                type="button"
                onClick={() => onSelectModel(opt.id)}
                className={`relative px-4 py-1.5 rounded-lg font-semibold transition flex items-center gap-2 cursor-pointer text-[13px] ${
                  isActive
                    ? 'bg-[#253e87] text-white shadow-md'
                    : 'text-white/80 hover:text-white hover:bg-white/10'
                }`}
                title={`${opt.label} — ${opt.subtitle}`}
              >
                <Icon className="w-3.5 h-3.5 opacity-90" />
                <span>{opt.label}</span>
                <span
                  className={`text-[11px] px-2 py-0.5 rounded font-mono ${
                    isActive
                      ? 'bg-white/20 text-white font-semibold'
                      : 'bg-black/20 text-white/70'
                  }`}
                >
                  {opt.badge}
                </span>
              </button>
            );
          })}

          {/* Bouton [ + ] */}
          <button
            id="add-model-btn"
            type="button"
            onClick={onAddNewModel}
            className="px-3 py-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/15 font-semibold transition flex items-center gap-1 cursor-pointer text-[13px]"
            title="Ajouter un nouveau modèle de vue"
          >
            <Plus className="w-4 h-4" />
          </button>
        </div>
      </div>

      <div className="hidden md:flex items-center gap-2 text-white/80 text-[12px] font-mono">
        <Sparkles className="w-3.5 h-3.5 text-[#3b82f6]" />
        <span>Côte d'Ivoire (CGI 2024-2026) · Données partagées synchronisées</span>
      </div>
    </div>
  );
};

import React from 'react';
import { Employee, ComputedEmployeePay, Period } from '../types/rhflow';
import { X, Download, ShieldCheck } from 'lucide-react';

interface PayslipModalProps {
  employee: Employee | null;
  computedPay: ComputedEmployeePay | null;
  period: Period;
  isOpen: boolean;
  onClose: () => void;
  onShowToast: (msg: string) => void;
}

export const PayslipModal: React.FC<PayslipModalProps> = ({
  employee,
  computedPay,
  period,
  isOpen,
  onClose,
  onShowToast,
}) => {
  if (!isOpen || !employee || !computedPay) return null;

  return (
    <div
      id="payslip-modal"
      className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
    >
      <div className="bg-white border border-[#E8E8E6] rounded-2xl max-w-2xl w-full p-8 space-y-6 shadow-2xl text-sm font-bold relative max-h-[90vh] overflow-y-auto">
        {/* Header modal */}
        <div className="flex justify-between items-center border-b border-[#E8E8E6] pb-4">
          <h3 className="font-bold text-base uppercase text-[#1F1F1E] flex items-center gap-2.5">
            <ShieldCheck className="w-5 h-5 text-[#253e87]" />
            Bulletin de Paie Officiel — {period.name}
          </h3>
          <button
            onClick={onClose}
            className="text-base font-bold text-[#1F1F1E] hover:text-neutral-500 cursor-pointer p-1"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Payslip content */}
        <div
          id="payslip-preview-content"
          className="space-y-4 border border-[#E8E8E6] rounded-xl p-6 bg-[#FAFAFA] text-[#1F1F1E]"
        >
          <div className="flex justify-between items-start border-b border-[#E8E8E6] pb-4 font-bold">
            <div>
              <h4 className="font-bold text-base text-[#1F1F1E]">ENTREPRISE ABC SARL</h4>
              <p className="text-xs text-neutral-500">12 Boulevard de la République, Abidjan Plateau (Côte d'Ivoire)</p>
              <p className="text-xs text-neutral-500">N° CC : 0102030 A • N° CNPS : 123456</p>
            </div>
            <div className="text-right">
              <p className="font-bold text-[#1F1F1E]">BULLETIN DE PAIE</p>
              <p className="text-xs text-[#253e87]">Période : {period.name}</p>
              <p className="text-xs text-neutral-500">Paiement par virement bancaire</p>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4 py-3 border-b border-[#E8E8E6] text-xs font-bold text-[#1F1F1E]">
            <div className="space-y-1">
              <p>
                <strong className="text-neutral-600">Salarié :</strong> {employee.name}
              </p>
              <p>
                <strong className="text-neutral-600">Département :</strong> {employee.dept}
              </p>
              <p>
                <strong className="text-neutral-600">Situation :</strong>{' '}
                {employee.maritalStatus === 'married' ? 'Marié(e)' : 'Célibataire'} ({employee.parts ?? 1} part
                {(employee.parts ?? 1) > 1 ? 's' : ''})
              </p>
            </div>
            <div className="space-y-1 text-right sm:text-left">
              <p>
                <strong className="text-neutral-600">Jours travaillés :</strong> {employee.days} / 30 jours
              </p>
              <p>
                <strong className="text-neutral-600">Matricule :</strong> {employee.matricule}
              </p>
              <p>
                <strong className="text-neutral-600">Devise :</strong> FCFA (XOF)
              </p>
            </div>
          </div>

          <table className="w-full text-left my-4 text-xs font-bold">
            <thead>
              <tr className="border-b border-[#253e87] text-[11px] uppercase tracking-wider text-neutral-600">
                <th className="py-2">LIBELLÉ</th>
                <th className="py-2 text-center">BASE / TAUX</th>
                <th className="py-2 text-right">GAIN</th>
                <th className="py-2 text-right">RETENUE</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#E8E8E6]">
              <tr>
                <td className="py-2">Salaire de Base Proratisé</td>
                <td className="text-center font-mono text-neutral-500">{employee.days}j / 30j</td>
                <td className="text-right font-montserrat text-neutral-900">
                  {computedPay.proratedBase.toLocaleString('fr-FR')}
                </td>
                <td className="text-right font-montserrat text-neutral-400">-</td>
              </tr>

              {employee.elements.map((el) => {
                const isGain = el.gain;
                const amt = el.type === 'transport' ? computedPay.proratedTransport : el.amount;
                return (
                  <tr key={el.id}>
                    <td className="py-2">
                      {el.label}
                      {el.type === 'transport' && computedPay.transportExempt > 0 && (
                        <span className="block text-[10px] text-neutral-400 font-normal">
                          Exonéré jusqu'à 30 000 FCFA
                        </span>
                      )}
                    </td>
                    <td className="text-center font-mono text-neutral-500">
                      {el.type === 'transport' ? `${employee.days}j` : '-'}
                    </td>
                    <td className="text-right font-montserrat text-neutral-900">
                      {isGain ? amt.toLocaleString('fr-FR') : '-'}
                    </td>
                    <td className="text-right font-montserrat text-neutral-900">
                      {!isGain ? amt.toLocaleString('fr-FR') : '-'}
                    </td>
                  </tr>
                );
              })}

              <tr className="bg-neutral-50 font-bold">
                <td className="py-2 text-[#253e87]">TOTAL SALAIRE BRUT</td>
                <td className="text-center font-mono text-neutral-500">-</td>
                <td className="text-right font-montserrat text-[#253e87]">
                  {computedPay.brut.toLocaleString('fr-FR')}
                </td>
                <td className="text-right font-montserrat text-neutral-400">-</td>
              </tr>

              <tr>
                <td className="py-2">Cotisation CNPS Retraite (Salariale)</td>
                <td className="text-center font-mono text-neutral-500">6,3%</td>
                <td className="text-right font-montserrat text-neutral-400">-</td>
                <td className="text-right font-montserrat text-neutral-900">
                  {computedPay.cnpsSalariale.toLocaleString('fr-FR')}
                </td>
              </tr>

              <tr>
                <td className="py-2">Couverture Maladie Universelle (CMU Salarié)</td>
                <td className="text-center font-mono text-neutral-500">Forfait</td>
                <td className="text-right font-montserrat text-neutral-400">-</td>
                <td className="text-right font-montserrat text-neutral-900">
                  {computedPay.cmu.toLocaleString('fr-FR')}
                </td>
              </tr>

              <tr>
                <td className="py-2">
                  Impôt sur les Traitements et Salaires (ITS 2024)
                  {computedPay.ricf > 0 && (
                    <span className="block text-[10px] text-neutral-400 font-normal">
                      Dont déduction RICF : -{computedPay.ricf.toLocaleString('fr-FR')} FCFA
                    </span>
                  )}
                </td>
                <td className="text-center font-mono text-neutral-500">Barème</td>
                <td className="text-right font-montserrat text-neutral-400">-</td>
                <td className="text-right font-montserrat text-neutral-900">
                  {computedPay.itsNet.toLocaleString('fr-FR')}
                </td>
              </tr>
            </tbody>
          </table>

          <div className="border-t-2 border-[#253e87] pt-3 flex justify-between font-bold text-base text-[#1F1F1E]">
            <span>NET À PAYER :</span>
            <span className="font-montserrat text-[#253e87] text-lg">
              {computedPay.net.toLocaleString('fr-FR')} FCFA
            </span>
          </div>

          <div className="text-[11px] text-neutral-500 pt-2 border-t border-neutral-200 grid grid-cols-2 font-montserrat">
            <div>
              <span>Assiette CNPS : {computedPay.assietteCNPS.toLocaleString('fr-FR')} FCFA</span>
            </div>
            <div className="text-right">
              <span>Coût Total Employeur : {computedPay.coutTotalEmployeur.toLocaleString('fr-FR')} FCFA</span>
            </div>
          </div>
        </div>

        {/* Modal actions */}
        <div className="flex justify-end gap-3">
          <button
            type="button"
            onClick={() => onShowToast('Génération et téléchargement du PDF officiel...')}
            className="btn-primary py-3 px-6 uppercase flex items-center gap-2 bg-[#253e87] text-white hover:bg-[#1c306d] font-bold rounded-lg cursor-pointer transition"
          >
            <Download className="w-4 h-4" />
            <span>Télécharger PDF</span>
          </button>
          <button
            type="button"
            onClick={onClose}
            className="btn-secondary py-3 px-6 uppercase bg-neutral-200 text-[#1F1F1E] hover:bg-neutral-300 font-bold rounded-lg cursor-pointer transition"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  );
};

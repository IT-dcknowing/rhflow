/**
 * Taux et plafonds officiels CNPS et CMU en Côte d'Ivoire
 */

export const CNPS_CONFIG = {
  /** Plafond mensuel Retraite CNPS : 3 375 000 FCFA (45 fois le SMIG) */
  CEILING_RETRAITE: 3375000,
  /** Plafond mensuel Prestations Familiales & AT/MP : 70 000 FCFA */
  CEILING_PF_AT: 70000,

  /** Taux Retraite Salariale : 6,3% */
  RATE_SALARIALE_RETRAITE: 0.063,

  /** Taux Patronaux */
  RATE_PATRONALE_RETRAITE: 0.077, // 7,7%
  RATE_PATRONALE_PF: 0.0575,      // 5,75% (plafond 70k)
  RATE_PATRONALE_AT: 0.03,        // 3,0% (plafond 70k)
  RATE_PATRONALE_MATERNITE: 0.0075, // 0,75% (plafond 70k)

  /** Taxes et contributions patronales associées */
  RATE_PATRONALE_CE: 0.028,       // Contribution Employeur 2,8%
  RATE_PATRONALE_FDFP_EFFECTIVE: 0.016 * 0.8, // 1,28% effectif FDFP

  /** Forfait Couverture Maladie Universelle (CMU) */
  CMU_FORFAIT_SALARIAL: 500,     // 500 FCFA / mois
  CMU_FORFAIT_PATRONAL: 500,     // 500 FCFA / mois

  /** Plafond mensuel d'exonération de la prime de transport */
  TRANSPORT_EXEMPTION_LIMIT: 30000,
};

export interface SocialContributionsResult {
  transportExempt: number;
  assietteCNPS: number;
  cnpsPlafonnee: number;
  cnpsSalariale: number;
  cmu: number;
  totalCotisSalariales: number;
  // Patronales
  cnpsRetraitePatronale: number;
  cnpsPFPatronale: number;
  cnpsATPatronale: number;
  cnpsMatPatronale: number;
  cmuPatronale: number;
  cePatronale: number;
  fdfpPatronale: number;
  totalChargesPatronales: number;
}

/**
 * Calcule l'ensemble des cotisations CNPS et CMU (salariales et patronales)
 */
export function calculateSocialContributions(
  brut: number,
  proratedTransport: number
): SocialContributionsResult {
  const transportExempt = Math.min(proratedTransport, CNPS_CONFIG.TRANSPORT_EXEMPTION_LIMIT);
  const assietteCNPS = Math.max(0, brut - transportExempt);

  // CNPS salariale
  const cnpsPlafonnee = Math.min(assietteCNPS, CNPS_CONFIG.CEILING_RETRAITE);
  const cnpsSalariale = Math.round(cnpsPlafonnee * CNPS_CONFIG.RATE_SALARIALE_RETRAITE);
  const cmu = CNPS_CONFIG.CMU_FORFAIT_SALARIAL;
  const totalCotisSalariales = cnpsSalariale + cmu;

  // CNPS patronale
  const cnpsRetraitePatronale = Math.round(cnpsPlafonnee * CNPS_CONFIG.RATE_PATRONALE_RETRAITE);
  const pfBase = Math.min(assietteCNPS, CNPS_CONFIG.CEILING_PF_AT);
  const cnpsPFPatronale = Math.round(pfBase * CNPS_CONFIG.RATE_PATRONALE_PF);
  const cnpsATPatronale = Math.round(pfBase * CNPS_CONFIG.RATE_PATRONALE_AT);
  const cnpsMatPatronale = Math.round(pfBase * CNPS_CONFIG.RATE_PATRONALE_MATERNITE);
  const cmuPatronale = CNPS_CONFIG.CMU_FORFAIT_PATRONAL;
  const cePatronale = Math.round(assietteCNPS * CNPS_CONFIG.RATE_PATRONALE_CE);
  const fdfpPatronale = Math.round(assietteCNPS * CNPS_CONFIG.RATE_PATRONALE_FDFP_EFFECTIVE);

  const totalChargesPatronales =
    cnpsRetraitePatronale +
    cnpsPFPatronale +
    cnpsATPatronale +
    cnpsMatPatronale +
    cmuPatronale +
    cePatronale +
    fdfpPatronale;

  return {
    transportExempt,
    assietteCNPS,
    cnpsPlafonnee,
    cnpsSalariale,
    cmu,
    totalCotisSalariales,
    cnpsRetraitePatronale,
    cnpsPFPatronale,
    cnpsATPatronale,
    cnpsMatPatronale,
    cmuPatronale,
    cePatronale,
    fdfpPatronale,
    totalChargesPatronales,
  };
}

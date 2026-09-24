/**
 * Réduction d'Impôt pour Charges de Famille (RICF) - Côte d'Ivoire
 * 
 * Règles légales :
 * - Célibataire, divorcé(e) ou veuf/veuve sans enfant : 1 part
 * - Marié(e) : 2 parts
 * - Enfant à charge : +0,5 part par enfant
 * - Plafond légal : 5 parts au maximum
 * - Réduction : 5 500 FCFA par demi-part au-delà de 1 part (2 demi-parts)
 */

export const RICF_CONFIG = {
  REDUCTION_PER_HALF_PART: 5500, // 5 500 FCFA
  MAX_PARTS: 5,
  MIN_PARTS: 1,
};

/**
 * Calcule le nombre de parts fiscales d'un salarié
 */
export function determineFiscalParts(
  maritalStatus: 'single' | 'married' = 'single',
  dependents: number = 0,
  explicitParts?: number
): number {
  if (explicitParts !== undefined && explicitParts !== null) {
    return Math.min(RICF_CONFIG.MAX_PARTS, Math.max(RICF_CONFIG.MIN_PARTS, explicitParts));
  }

  let parts = maritalStatus === 'married' ? 2 : 1;
  parts += Math.max(0, dependents) * 0.5;
  return Math.min(RICF_CONFIG.MAX_PARTS, Math.max(RICF_CONFIG.MIN_PARTS, parts));
}

/**
 * Calcule le montant de la déduction RICF en FCFA
 */
export function calculateRICF(parts: number): number {
  const boundedParts = Math.min(RICF_CONFIG.MAX_PARTS, Math.max(RICF_CONFIG.MIN_PARTS, parts));
  const totalDemiParts = Math.round(boundedParts * 2);
  const demiPartsAuDela1 = Math.max(0, totalDemiParts - 2);
  return demiPartsAuDela1 * RICF_CONFIG.REDUCTION_PER_HALF_PART;
}

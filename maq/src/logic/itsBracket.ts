/**
 * Barème ITS 2024 de Côte d'Ivoire (CGI art. 119 bis)
 * Tranches d'imposition mensuelles :
 * - 0 à 75 000 FCFA : 0%
 * - 75 001 à 240 000 FCFA : 16%
 * - 240 001 à 800 000 FCFA : 21%
 * - 800 001 à 2 400 000 FCFA : 24%
 * - 2 400 001 à 8 000 000 FCFA : 28%
 * - Au-delà de 8 000 000 FCFA : 32%
 */

export interface ITSBracket {
  limit: number;
  rate: number;
}

export const ITS_BRACKETS_2024: ITSBracket[] = [
  { limit: 75000, rate: 0 },
  { limit: 240000, rate: 0.16 },
  { limit: 800000, rate: 0.21 },
  { limit: 2400000, rate: 0.24 },
  { limit: 8000000, rate: 0.28 },
  { limit: Infinity, rate: 0.32 },
];

/**
 * Calcule l'ITS brut mensuel selon le barème progressif officiel 2024
 * @param baseITS Assiette imposable (Assiette CNPS)
 */
export function calculateRawITS(baseITS: number): number {
  if (baseITS <= 75000) return 0;

  let rawITS = 0;
  if (baseITS > 8000000) {
    rawITS += (baseITS - 8000000) * 0.32;
    rawITS += (8000000 - 2400000) * 0.28;
    rawITS += (2400000 - 800000) * 0.24;
    rawITS += (800000 - 240000) * 0.21;
    rawITS += (240000 - 75000) * 0.16;
  } else if (baseITS > 2400000) {
    rawITS += (baseITS - 2400000) * 0.28;
    rawITS += (2400000 - 800000) * 0.24;
    rawITS += (800000 - 240000) * 0.21;
    rawITS += (240000 - 75000) * 0.16;
  } else if (baseITS > 800000) {
    rawITS += (baseITS - 800000) * 0.24;
    rawITS += (800000 - 240000) * 0.21;
    rawITS += (240000 - 75000) * 0.16;
  } else if (baseITS > 240000) {
    rawITS += (baseITS - 240000) * 0.21;
    rawITS += (240000 - 75000) * 0.16;
  } else if (baseITS > 75000) {
    rawITS += (baseITS - 75000) * 0.16;
  }

  return Math.round(rawITS);
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sector;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE MÉCANIQUE, INDUSTRIES EXTRACTIVES ET PROSPECTION MINIÈRE,INDUSTRIE ALIMENTAIRE, INDUSTRIE DES CORPS GRAS, INDUSTRIE CHIMIQUE, TRANSPORT ET AUTRES INDUSTRIES',
                'slug' => 'industry',
                'description' => 'Secteur industriel regroupant mécanique, extractives, alimentaire, chimique et autres industries',
                'sort_order' => 1,
            ],
            [
                'name' => 'SECURITE - GARDIENNAGE - SURVEILLANCE',
                'slug' => 'security',
                'description' => 'Secteur de la sécurité privée, gardiennage et surveillance',
                'sort_order' => 2,
            ],
            [
                'name' => 'SECTEUR SECURITÉ PRIVÉE',
                'slug' => 'private-security',
                'description' => 'Secteur de la sécurité privée',
                'sort_order' => 3,
            ],
            [
                'name' => 'SECTEUR TRANSPORT DE FONDS ET VALEURS',
                'slug' => 'transport-fond',
                'description' => 'Secteur du transport de fonds et valeurs',
                'sort_order' => 4,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE DU BOIS',
                'slug' => 'manufacturing',
                'description' => 'Industrie du bois et transformation',
                'sort_order' => 5,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE TEXTILE',
                'slug' => 'industry-textile',
                'description' => 'Industrie textile et confection',
                'sort_order' => 6,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / PRODUCTION AGRICOLE',
                'slug' => 'industry-agricole',
                'description' => 'Production agricole industrielle',
                'sort_order' => 7,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE DU SUCRE',
                'slug' => 'energy',
                'description' => 'Industrie du sucre et agroalimentaire',
                'sort_order' => 8,
            ],
            [
                'name' => 'SECTEUR HOTELLERIE',
                'slug' => 'food-industry',
                'description' => 'Secteur de l\'hôtellerie et restauration',
                'sort_order' => 9,
            ],
            [
                'name' => 'SECTEUR BÂTIMENT, TRAVAUX PUBLICS ET ACTIVITES CONNEXES',
                'slug' => 'batiment-travaux',
                'description' => 'Bâtiment, travaux publics et activités connexes',
                'sort_order' => 10,
            ],
            [
                'name' => 'SECTEUR DOCKERS',
                'slug' => 'telecommunications',
                'description' => 'Secteur des dockers et manutention portuaire',
                'sort_order' => 11,
            ],
            [
                'name' => 'SECTEUR COMMERCE, DISTRIBUTION, NÉGOCE ET PROFESSIONS LIBÉRALES',
                'slug' => 'commerce-distribution',
                'description' => 'Commerce, distribution, négoce et professions libérales',
                'sort_order' => 12,
            ],
            [
                'name' => 'SECTEUR AGRICOLE (CAFE - CACAO - RIZ - COTON)',
                'slug' => 'agricole-cafe-cacao-riz-coton',
                'description' => 'Production agricole spécialisée (café, cacao, riz, coton)',
                'sort_order' => 13,
            ],
            [
                'name' => 'SECTEUR AGRICOLE (PLANTATIONS AUTRES)',
                'slug' => 'agricole-autres',
                'description' => 'Plantations agricoles diverses',
                'sort_order' => 14,
            ],
            [
                'name' => 'SECTEUR ELEVAGE',
                'slug' => 'elevage',
                'description' => 'Secteur de l\'élevage et productions animales',
                'sort_order' => 15,
            ],
            [
                'name' => 'SECTEUR FORESTIER, HARAS, ENTREPRISES DE MARAIS SALANTS D\'ENTRETIEN ET MISE EN ÉTAT DES JARDINS',
                'slug' => 'forestier-haras',
                'description' => 'Secteur forestier, haras et entretien espaces verts',
                'sort_order' => 16,
            ],
            [
                'name' => 'SECTEUR BANQUES ET ASSURANCES / BANQUES',
                'slug' => 'banques',
                'description' => 'Secteur bancaire',
                'sort_order' => 17,
            ],
            [
                'name' => 'SECTEUR BANQUES ET ASSURANCES / ASSURANCES',
                'slug' => 'assurances',
                'description' => 'Secteur des assurances',
                'sort_order' => 18,
            ],
            [
                'name' => 'SECTEUR PÉTROLIER / ENTREPRISES PETROLIERES D\'EXPLORATION – PRODUCTION',
                'slug' => 'petrolier-exploration',
                'description' => 'Exploration et production pétrolière',
                'sort_order' => 19,
            ],
            [
                'name' => 'SECTEUR PÉTROLIER / ENTREPRISES PETROLIERES DE DISTRIBUTION',
                'slug' => 'petrolier-distribution',
                'description' => 'Distribution pétrolière',
                'sort_order' => 20,
            ],
            [
                'name' => 'SECTEUR MARITIME / ARMEMENT AU COMMERCE',
                'slug' => 'maritime-armement',
                'description' => 'Armement maritime au commerce',
                'sort_order' => 21,
            ],
            [
                'name' => 'SECTEUR MARITIME / PÊCHE FRAÎCHE',
                'slug' => 'maritime-peche-fraiche',
                'description' => 'Pêche fraîche',
                'sort_order' => 22,
            ],
            [
                'name' => 'SECTEUR MARITIME / LA PÊCHE AU LARGE',
                'slug' => 'maritime-peche-large',
                'description' => 'Pêche au large',
                'sort_order' => 23,
            ],
            [
                'name' => 'SECTEUR MARITIME / LA PÊCHE CÔTIÈRE',
                'slug' => 'maritime-peche-cotiere',
                'description' => 'Pêche côtière',
                'sort_order' => 24,
            ],
            [
                'name' => 'SECTEUR DU TRANSPORT AUXILIAIRES',
                'slug' => 'transport-auxiliaires',
                'description' => 'Transport auxiliaires',
                'sort_order' => 25,
            ],
            [
                'name' => 'SECTEUR DU TRANSPORT AÉRIEN',
                'slug' => 'transport-aerien',
                'description' => 'Transport aérien',
                'sort_order' => 26,
            ],
            [
                'name' => 'SECTEUR GENS DE MAISON',
                'slug' => 'gens-maison',
                'description' => 'Secteur des employés de maison',
                'sort_order' => 27,
            ],
            [
                'name' => 'SECTEUR NETTOYAGE & SALUBRITÉ',
                'slug' => 'nettoyage-salubrite',
                'description' => 'Secteur du nettoyage et salubrité',
                'sort_order' => 28,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE DE TRANSFORMATION DE THON',
                'slug' => 'industrie-thon',
                'description' => 'Transformation de thon',
                'sort_order' => 29,
            ],
            [
                'name' => 'SECTEUR INDUSTRIEL / INDUSTRIE POLYGRAPHIQUE',
                'slug' => 'industrie-polygraphique',
                'description' => 'Industrie polygraphique',
                'sort_order' => 30,
            ],
            [
                'name' => 'SECTEUR DU TOURISME',
                'slug' => 'tourisme',
                'description' => 'Secteur du tourisme',
                'sort_order' => 31,
            ]
        ];

        foreach ($sectors as $sector) {
            Sector::create($sector);
        }
    }
}

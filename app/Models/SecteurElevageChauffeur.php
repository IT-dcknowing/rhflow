<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecteurElevageChauffeur extends Model
{
    use HasFactory;

    protected $table = 'secteur_elevage_employes';
    
    protected $fillable = [
        'categorie',
        'definition',
        'salaire_minima_horaire',
        'salaire_minima_mensuel',
        'type_poste',
        'id_secteur',
        'company_id'
    ];

    protected $casts = [
        'salaire_minima_horaire' => 'float',
        'salaire_minima_mensuel' => 'float',
        'id_secteur' => 'integer',
        'company_id' => 'integer',
    ];

    public function secteur()
    {
        return $this->belongsTo(Sector::class, 'id_secteur');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

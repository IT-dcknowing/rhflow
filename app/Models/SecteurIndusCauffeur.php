<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecteurIndusCauffeur extends Model
{
    use HasFactory;

    protected $table = 'sec_indus_chauffeurs';
    
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
        'salaire_minima_horaire' => 'integer',
        'salaire_minima_mensuel' => 'integer',
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

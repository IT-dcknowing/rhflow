<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecteurMaritimePoly extends Model
{
    use HasFactory;

    protected $table = 'secteur_maritime_polies';
    
    protected $fillable = [
        'categorie',
        'navi_cotiere',
        'cabot_inter',
        'long_cours',
        'type_poste',
        'id_secteur',
        'company_id'
    ];

    protected $casts = [
        'navi_cotiere' => 'integer',
        'cabot_inter' => 'integer',
        'long_cours' => 'integer',
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

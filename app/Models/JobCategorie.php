<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategorie extends Model
{
    use HasFactory;

    protected $table = 'job_categories';

    protected $fillable = [
        'title',
        'id_secteur',
    ];

    public function secteur()
    {
        return $this->belongsTo(Sector::class, 'id_secteur');
    }
}

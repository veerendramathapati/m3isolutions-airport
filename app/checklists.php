<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class checklists extends Model
{use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'p_m_id', 'co_sl','created_by','created_at','updated_by','updated_at'
    ];
    /**
     * Get the restroom that owns the parameter.
     */
    public function restroom_master()
    {
        return $this->belongsTo(restroom_master::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class parameter_lists extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'r_m_id','p_m_id', 'co_sl','created_by','created_at','updated_by','updated_at'
    ];
    /**
     * Get the restroom that owns the parameter.
     */
    public function restroom_master()
    {
        return $this->belongsTo(restroom_master::class);
    }
}

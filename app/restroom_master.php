<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class restroom_master extends Model
{
    use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'r_name', 'location','unique_identifier','created_by','created_at','updated_by','updated_at'
    ];

    /**
     * Get the parameter for the restroom.
     */
    public function parameter()
    {
        return $this->hasMany(checklists::class);
    }
}

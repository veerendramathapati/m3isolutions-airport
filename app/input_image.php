<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class input_image extends Model
{
    use HasApiTokens, Notifiable;

    /**
 * The attributes that are mass assignable.
 *
 * @var array
 */
    protected $fillable=[
            'input_id','img_path','co_sl','created_by','created_at','updated_by','updated_at'
];
    protected $table = 'input_image';
}

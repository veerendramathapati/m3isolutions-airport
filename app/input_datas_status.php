<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class input_datas_status extends Model
{
protected $table="input_datas_status";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'input_id', 'status','co_sl','updated_by','created_at'
    ];

}

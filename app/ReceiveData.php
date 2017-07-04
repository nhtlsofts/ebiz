<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiveData extends Model
{
    //
    protected $table = 'receive_data';
    
    protected $casts = [
        'data' => 'json'
    ];
}

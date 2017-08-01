<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiveData extends Model
{
    //

    protected $table = null;

    public function __construct($user_id) {
       $this->table = 'receive_data_'.$user_id;
    }

    
    protected $casts = [
        'data' => 'json'
    ];
}

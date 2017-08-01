<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function __construct() {
       $this->table = 'Customer';
    }

}

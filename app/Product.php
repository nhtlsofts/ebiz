<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function __construct() {
       $this->table = 'product';
    }

}

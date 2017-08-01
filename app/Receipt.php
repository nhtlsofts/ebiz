<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
	protected $fillable = ['code','customer_id','page_id','facebookuser','address','$address','tel','detailamount','detaildiscount','discount','vat', 'shipcost','total','paid'];

    public function __construct() {
       $this->table = 'receipt';
    }

}


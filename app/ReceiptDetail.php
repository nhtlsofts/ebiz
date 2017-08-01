<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
	protected $fillable = ['receipt','product','product_name','unit','price','quanlity','discount','amount','facebookuser'];

    public function __construct() {
       $this->table = 'receipt_detail';
    }

}


<?php
// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;
    protected $table = "'NYS_CUSTOMER'";
    protected $primaryKey = 'CODIGO_CLIENTE';
    public $timestamps = false;

}

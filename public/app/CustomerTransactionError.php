<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CustomerTransactionError extends Model
{
    
    protected $fillable = [
        "customer_id",
        "error_given"
    ];
}

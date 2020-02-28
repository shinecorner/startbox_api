<?php

namespace App\Models;

use App\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasCreator;

    public $fillable = ['message'];

    public $dates = ['resolved_at'];
}

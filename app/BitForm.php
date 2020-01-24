<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitForm extends Model
{
    protected $table = 'bitform';
    protected $primaryKey = 'bitform_id';
    protected $fillable = [
        'bitform_bittable_id',
        'bitform_label',
        'bitform_input',
        'bitform_type',
        'bitform_url',
        'bitform_rules',
        'bitform_messages',
    ];
}

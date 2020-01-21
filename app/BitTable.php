<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitTable extends Model
{
    protected $table = 'bittable';
    protected $primaryKey = 'bittable_id';
    protected $fillable = [
        'bittable_parent_id',
        'bittable_name',
        'bittable_type',
        'bittable_length',
        'bittable_default',
        'bittable_attributes',
        'bittable_join',
        'bittable_join_to_id',
    ];
    public function child()
    {
        return $this->hasMany(BitTable::class,'bittable_parent_id',$this->primaryKey);
    }
    public function parent()
    {
        return $this->belongsTo(BitTable::class,'bittable_parent_id',$this->primaryKey);
    }
    public function join()
    {
        return $this->belongsTo(BitTable::class,'bittable_join_to_id',$this->primaryKey);
    }
}

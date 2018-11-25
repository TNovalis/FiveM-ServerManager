<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';

    protected $primaryKey = 'option';

    protected $casts = ['value' => 'string'];

    protected function getCastType($key)
    {
        if ($key == 'value' && ! empty($this->type)) {
            return $this->type;
        } else {
            return parent::getCastType($key);
        }
    }
}

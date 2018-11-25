<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $guarded = [];

    public function server()
    {
        return $this->belongsTo('App\Server', 'server_name', 'name');
    }
}

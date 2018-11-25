<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';

    protected $primaryKey = 'name';

    protected $casts = ['status' => 'boolean'];

    public function getPidAttribute()
    {
        $status = exec("ps auxw | grep -i fivem-$this->name | grep -v grep | awk '{print $2}'");

        if (empty($status)) {
            return false;
        }

        return intval($status);
    }

    public function getCrashedAttribute()
    {
        if ($this->pid && ! $this->status) {
            $this->status = true;
            $this->save();
        }

        if ($this->pid || ! $this->status) {
            return false;
        }

        if (empty($this->pid)) {
            return true;
        }
    }

    public function backups()
    {
        return $this->hasMany('App\Backup', 'server_name');
    }
}

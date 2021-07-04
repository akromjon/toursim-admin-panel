<?php

namespace App\Models\File\Traits;

trait Methods
{

    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])->format('d.m.Y H:i:s');
    }

    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])->format('d.m.Y H:i:s');
    }

    public function getDeletedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['deleted_at'])->format('d.m.Y H:i:s');
    }
}

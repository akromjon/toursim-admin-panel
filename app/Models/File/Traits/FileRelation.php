<?php

namespace App\Models\File\Traits;

trait FileRelation
{

    public function fileable()
    {
        return $this->morphTo();
    }

}

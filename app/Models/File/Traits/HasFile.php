<?php

namespace App\Models\File\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\File\File;

trait HasFile
{
    public function hasOneFile()
    {

        if (!$this->callRelation()->exists()) {
            $this->callRelation()->create();
        }

        return $this->callRelation();
    }

    public function callRelation(): MorphOne
    {

        return $this->morphOne(File::class, 'fileable');
    }
}

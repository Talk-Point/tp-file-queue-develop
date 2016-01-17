<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelExample extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tasks()
    {
        return $this->morphMany('TPTaskRunner\Models\Task', 'taskable');
    }
}

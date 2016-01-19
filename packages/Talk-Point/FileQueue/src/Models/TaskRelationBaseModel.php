<?php

namespace TPTaskRunner\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Task Relation base Model
 * @package TPTaskRunner\Models
 */
class TaskRelationBaseModel extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tasks()
    {
        return $this->morphMany('TPTaskRunner\Models\Task', 'taskable');
    }
}

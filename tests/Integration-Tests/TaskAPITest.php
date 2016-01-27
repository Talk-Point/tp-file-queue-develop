<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskAPITest extends TestCase
{
    public function test_task_api_controller_visit()
    {
        $task = \TPTaskRunner\Models\Task::first();

        $this->visit(URL::route('api.v1.tasks.index'))
             ->seeJson(['id' => $task->id]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskModelTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_task_init()
    {
        $task = \TPTaskRunner\Models\Task::createTaskWithData('Class', ['string' => 'data']);
        $task->save();

        /** @var \TPTaskRunner\Models\Task $task_load */
        $task_load = \TPTaskRunner\Models\Task::findOrFail($task->id);
        $this->assertNotNull($task_load);

        $o = $task_load->getJSONData();
        $this->assertNotNull($o);
        $this->assertEquals('data', $o->string);
    }
}

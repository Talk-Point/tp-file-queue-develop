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

    public function test_cron_tasks()
    {
        /** @var \TPTaskRunner\Models\Task $task_1 */
        $task_1 = factory(\TPTaskRunner\Models\Task::class)->create();
        $task_1->next_run_at = \Carbon\Carbon::now()->addDay(1);
        $task_1->save();
        /** @var \TPTaskRunner\Models\Task $task_2 */
        $task_2 = factory(\TPTaskRunner\Models\Task::class)->create();


        $query_1_count = \TPTaskRunner\Models\Task::cron()->count();
        $query_2_count = \TPTaskRunner\Models\Task::cron(\Carbon\Carbon::now()->addDay(2))->count();

        $this->assertNotEquals($query_1_count, $query_2_count);
        $this->assertTrue($query_1_count < $query_2_count);
    }
}

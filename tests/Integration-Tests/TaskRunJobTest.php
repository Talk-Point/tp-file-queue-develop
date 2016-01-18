<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskSuccess extends \TPTaskRunner\Jobs\Tasks\BaseTask {}
class TaskFailureReturnFalse extends \TPTaskRunner\Jobs\Tasks\BaseTask
{
    public function run()
    {
        return [false, 'Einfach mal so false zurueckgegeben'];
    }
}
class TaskFailureThrowException extends \TPTaskRunner\Jobs\Tasks\BaseTask
{
    public function run()
    {
        throw new Exception('Test Exceptin');
    }
}

class TaskRunJobTest extends TestCase
{
    public function createTaskWithJobClass($job_class)
    {
        $model_example = factory(\App\ModelExample::class)->create();
        $this->assertNotNull($model_example);


        $task = new \TPTaskRunner\Models\Task();
        $task->job_class = $job_class;
        $model_example->tasks()->save($task);
        return [$task, $model_example];
    }

    public function test_task_success()
    {
        list($task, $order) = $this->createTaskWithJobClass('TaskSuccess');
        $this->visit('api/v1/tasks/run/'.strval($task->id))
             ->seeJson(['start' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => true,
            'is_failure' => false
        ]);
    }

    public function test_task_failure_return_false()
    {
        list($task, $order) = $this->createTaskWithJobClass('TaskFailureReturnFalse');
        $this->visit('api/v1/tasks/run/'.strval($task->id))
            ->seeJson(['start' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => false,
            'is_failure' => true
        ]);
    }


    public function test_task_failure_throw_exception()
    {
        list($task, $order) = $this->createTaskWithJobClass('TaskFailureThrowException');
        $this->visit('api/v1/tasks/run/'.strval($task->id))
            ->seeJson(['start' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => false,
            'is_failure' => true
        ]);
    }

    public function test_task_class_not_exists()
    {
        list($task, $order) = $this->createTaskWithJobClass('TaskClassNotExists');
        $this->visit('api/v1/tasks/run/'.strval($task->id))
            ->seeJson(['start' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => false,
            'is_failure' => true
        ]);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use TPTaskRunner\Jobs\Tasks\BaseTask;

class TaskSuccess extends BaseTask {}
class TaskFailureReturnFalse extends BaseTask
{
    public function run()
    {
        return [false, 'Einfach mal so false zurueckgegeben'];
    }
}
class TaskFailureThrowException extends BaseTask
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

        $task = \TPTaskRunner\Models\Task::createTask($job_class);
        $model_example->tasks()->save($task);
        return [$task, $model_example];
    }

    public function test_task_success()
    {
        /** @var \TPTaskRunner\Models\Task $task */
        list($task, $order) = $this->createTaskWithJobClass('TaskSuccess');
        $task->setJSONData(['string' => 'data']);
        $this->visit('api/v1/tasks/run/'.strval($task->id))
             ->seeJson(['run' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => true,
            'is_failure' => false,
            'data' => "{\"string\":\"data\"}"
        ]);
    }

    public function test_task_failure_return_false()
    {
        list($task, $order) = $this->createTaskWithJobClass('TaskFailureReturnFalse');
        $this->visit('api/v1/tasks/run/'.strval($task->id))
            ->seeJson(['run' => true]);
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
            ->seeJson(['run' => true]);
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
            ->seeJson(['run' => true]);
        $this->seeInDatabase('tasks', [
            'id' => $task->id,
            'is_runned' => true,
            'is_success' => false,
            'is_failure' => true
        ]);
    }
}

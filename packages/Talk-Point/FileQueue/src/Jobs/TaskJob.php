<?php

namespace TPFileQueue\Jobs;

use TPFileQueue\Jobs\Tasks\BaseTask;
use TPFileQueue\Models\Task;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Job to run a Task
 * @package TPFileQueue\Jobs
 */
class TaskJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Task Primary Key
     * @var string Task Id
     */
    public $task_id;

    /**
     * Create a new job instance.
     *
     * @param string $class Task Class
     */
    public function __construct($task_id)
    {
        $this->task_id = $task_id;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle()
    {
        /** @var Task $task */
        $task = Task::find($this->task_id);
        if (is_null($task)) {
            throw new \Exception('TaskJob not found task with id', ['task_id' => $this->task_id]);
        }
        $task->start_running();

        if (!class_exists($task->job_class)) {
            $task->failure('Class '.strval($task->job_class).' not exists');
            $this->delete();
            return null;
        }
        $className = $task->job_class;
        /** @var BaseTask $task */
        $object = $task->taskable()->first();
        $job = new $className($object);

        try {
            list($rv, $errormessage) = $job->run();
            if ($rv === true) {
                $job->success();
                $task->success();
            } else {
                $job->failure(new Exception('Job '.$className.' return value false and message: '.$errormessage));
                $task->failure('Job '.$className.' return value false and error message:'.$errormessage);
            }
        } catch (Exception $e) {
            $job->failure($e);
            $task->failure($e);
        }

        $this->delete();
    }
}

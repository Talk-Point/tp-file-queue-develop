<?php

namespace TPTaskRunner\Jobs\Tasks;

use Log;
use TPTaskRunner\Models\Task;

/**
 * Class BaseTask
 * @package TPTaskRunner\Jobs\Tasks
 */
abstract class BaseTask
{
    protected $object;
    /**
     * @var Task task instance thats running
     */
    public $task;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function run()
    {
        Log::debug('BaseTask::run()');
        return [true, 'Error Message'];
    }

    public function failure($exception)
    {
        Log::debug('BaseTask::failure()');
    }

    public function success()
    {
        Log::debug('BaseTask::success()');
    }
}
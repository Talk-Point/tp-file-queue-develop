<?php

namespace TPFileQueue\Jobs\Tasks;

use Log;

/**
 * Class BaseTask
 * @package TPFileQueue\Jobs\Tasks
 */
abstract class BaseTask
{
    protected $object;

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
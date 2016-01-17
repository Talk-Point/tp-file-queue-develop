<?php

namespace TPTaskRunner\Models;

use TPTaskRunner\Jobs\TaskJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Exception;
use Log;

/**
 * Task
 *
 * Ein task ist ein einzelener Job der ausgeführt wird und speichert ob er fehlgeschlagen ist oder
 * seine Zustandsänderungen speichert.
 *
 * @package TPTaskRunner\Models
 * @property integer id
 * @property string job_class
 * @property bool is_runned
 * @property bool is_failure
 * @property Carbon is_failure_at
 * @property bool is_success
 * @property Carbon is_success_at
 * @property Carbon next_run_at
 * @property string failure_message
 */
class Task extends Model
{
    use DispatchesJobs;

    /**
     * Append to Object
     * @var array
     */
    protected $appends = ['links'];

    /**
     * Attribute Links
     * @return mixed
     */
    public function getLinksAttribute()
    {
        return [
            'show' => URL::route('api.v1.tasks.show', $this->id),
            'run' => URL::route('api.v1.tasks.run', $this->id),
            'rerun' => URL::to('/api/v1/tasks/run/'.$this->id.'/?force=true'),
        ];
    }

    /**
     * Create Task
     * @param string $job_class
     * @return Task
     */
    public static function createTask($job_class)
    {
        $task = new Task();
        $task->job_class = $job_class;
        return $task;
    }

    /**
     * Task constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'is_failure_at', 'is_success_at', 'next_run_at'];

    /**
     * Get the order for this task
     *
     * One To Many Relation
     */
    public function taskable()
    {
        return $this->morphTo();
    }

    /**
     * Scope: Tasks that run by cron
     * @param $query
     * @return mixed
     * @deprecated
     */
    public function scopeCron($query)
    {
        return $query->where('is_runned', false);
    }

    /**
     * Scope Success Tasks
     * @param $query
     * @return mixed
     */
    public function scopeSuccess($query)
    {
        return $query->where('is_runned', true)->where('is_success', true);
    }

    /**
     * Erzeugt den job des Tasks
     * @param bool $force ob er erneut ausgeführt werden soll
     * @return bool if he started the job
     */
    public function run($force=false)
    {
        if ($force==false) {
            if ($this->is_success) {
                return false;
            }
        }
        $this->dispatch(new TaskJob($this->id));
        return true;
    }

    /**
     * Tasks: start running action
     */
    public function start_running()
    {
        Log::debug('Task::start_running', ['task_id' => $this->id]);
        $this->is_runned = true;
        $this->is_runned_at = Carbon::now();
        $this->save();
    }

    /**
     * Tasks: running and is success action
     */
    public function success()
    {
        Log::debug('Task::sucess', ['task_id' => $this->id]);
        $this->is_success = true;
        $this->is_success_at = Carbon::now();
        $this->save();
    }

    /**
     * Tasks: running and get failure action
     * @param string $message
     */
    public function failure($message='Kein Fehler angegeben')
    {
        Log::debug('Task::failure', ['task_id' => $this->id]);
        $this->is_failure = true;
        $this->is_success = false;
        $this->is_failure_at = Carbon::now();
        if ($message instanceof Exception) {
            $this->failure_message = $message->getMessage().'\n'.$message->getTraceAsString();
        } else {
            $this->failure_message = strval($message);
        }
        $this->save();
    }
}

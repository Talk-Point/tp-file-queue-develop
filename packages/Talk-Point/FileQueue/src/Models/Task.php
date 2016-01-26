<?php

namespace TPTaskRunner\Models;

use TPTaskRunner\Jobs\TaskJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\URL;
use Exception;
use Log;

/**
 * Task
 *
 * Ein task ist ein einzelener Job der ausgeführt wird und speichert ob er fehlgeschlagen ist oder
 * seine Zustandsänderungen speichert.
 *
 * @package TPTaskRunner\Models
 * @property integer    id              PK
 * @property string     job_class       Class that would be run
 * @property string     data            Text for saving Data for the Task
 * @property bool       is_runned       if the tasks was running
 * @property bool       is_failure      if the task has a failure
 * @property Carbon     is_failure_at   if task has failure then this is the date
 * @property bool       is_success      if task is success running
 * @property Carbon     is_success_at   if task is susccess this is the date
 * @property Carbon     next_run_at     when the tasks would start
 * @property string     failure_message the error message from the class thats runned
 */
class Task extends Model
{
    use DispatchesJobs;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    public $casts = ['is_runned' => 'boolean', 'is_success' => 'boolean', 'is_failure' => 'boolean',];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'is_failure_at', 'is_success_at', 'next_run_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['job_class', 'data', 'next_run_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'is_runned', 'is_failure', 'is_failure_at', 'is_success', 'is_success_at', 'failure_message'];

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
     * Konstruktor create task
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
     * Konstruktor create task with data
     * @param $job_class
     * @param $array
     * @return Task
     */
    public static function createTaskWithData($job_class, $array)
    {
        $task = Task::createTask($job_class);
        $task->setJSONData($array);
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
     * Get the order for this task
     *
     * One To Many Relation
     */
    public function taskable()
    {
        return $this->morphTo();
    }

    /**
     * Save Array in Task
     * @param $array
     */
    public function setJSONData($array)
    {
        $this->data = json_encode($array);
        $this->save();
    }

    /**
     * Get Array in Task
     * @return mixed
     */
    public function getJSONData()
    {
        return json_decode($this->data);
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
        if ($force === false) {
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

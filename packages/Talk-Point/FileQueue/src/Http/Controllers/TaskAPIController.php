<?php

namespace TPTaskRunner\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use TPREST\Http\RESTQuery;
use TPTaskRunner\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class TaskAPIController
 * @package TPTaskRunner\Http\Controllers
 */
class TaskAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            /** @var Collection $queues */
            $tasks = RESTQuery::create(Task::class)->query()->get();
            return response()->json($tasks);
        } catch (QueryException $e) {
            return response()->json(['message' => 'DB Query Exception', 'invalid' => $e->errorInfo[2]], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'DB Query Exception'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $task = Task::create(Input::all());
            $task->save();
            return response()->json(['created' => true, 'task' => $task], 201);
        } catch (Exception $e) {
            return response()->json(['created' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            /** @var Task $task */
            $task = Task::findOrFail($id);

            $task->fill(Input::all());
            $task->save();
            return response()->json(['updated' => true], 200);
        } catch (Exception $e) {
            Log::emergency('TaskAPIController::update can not update task', ['task_id' => $id]);
            return response()->json(['updated' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json(['destroy' => true]);
        } catch (Exception $e) {
            Log::emergency('TaskAPIController::destroy can not destroy task', ['task_id' => $id]);
            return response()->json(['destroy' => true], 500);
        }
    }

    /**
     * Erzeugt einen Job für den Task
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function run($id)
    {
        $task = Task::findOrFail($id);
        $force_value = Input::get('force', false);
        $rv = $task->run($force=$force_value);
        return response()->json(['start' => $rv]);
    }
}

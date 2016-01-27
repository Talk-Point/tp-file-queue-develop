<?php

namespace TPTaskRunner\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
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
        $tasks = Task::all();


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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

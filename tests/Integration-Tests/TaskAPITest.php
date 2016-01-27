<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaskAPITest extends TestCase
{
    public function test_index()
    {
        $task = \TPTaskRunner\Models\Task::get()->first();
        $this->assertNotNull($task);

        $this->visit(URL::route('api.v1.tasks.index'))
             ->seeJson(['id' => $task->id]);
    }

    public function test_show()
    {
        $task = \TPTaskRunner\Models\Task::get()->first();
        $this->assertNotNull($task);

        $this->visit(URL::route('api.v1.tasks.show', $task->id))
            ->seeJsonStructure(['id',
                'job_class',
                'is_runned',
                'is_runned_at',
                'is_failure',
                'is_failure_at',
                'is_success',
                'is_success_at',
                'next_run_at',
                'taskable_id',
                'taskable_type',
                'failure_message',
                'created_at',
                'updated_at',
                'data',
                'links' => ['show', 'run', 'rerun'],
            ])
            ->seeJson(['id' => $task->id])
            ->seeStatusCode(200);
    }

    public function test_store()
    {
        $faker = Faker\Factory::create();
        $id = $faker->uuid;

        $this->dontSeeInDatabase('tasks', ['job_class' => $id]);

        $this->post(URL::route('api.v1.tasks.store'), [
            'job_class' => $id,
            'data' => 'test-data'
        ])
            ->seeJson(['created' => true])
            ->seeStatusCode(201);

        $this->seeInDatabase('tasks', ['job_class' => $id, 'data' => 'test-data']);
    }

    public function test_update()
    {
        $faker = Faker\Factory::create();

        $task = factory(\TPTaskRunner\Models\Task::class)->create();

        $job_class = $faker->uuid;
        $data = $faker->uuid;
        $this->put(URL::route('api.v1.tasks.update', $task->id), [
            'job_class' => $job_class,
            'data' => $data
        ])
            ->seeJson(['updated' => true])
            ->seeStatusCode(200);

        $this->seeInDatabase('tasks', ['id' => $task->id, 'job_class' => $job_class, 'data' => $data]);
    }

    public function test_destroy()
    {
        $task = \TPTaskRunner\Models\Task::get()->first();
        $this->assertNotNull($task);

        $this->seeInDatabase('tasks', ['id' => $task->id]);

        $this->delete(URL::route('api.v1.tasks.destroy', $task->id))
            ->seeJson(['destroy' => true])
            ->seeStatusCode(200);

        $this->dontSeeInDatabase('tasks', ['id' => $task->id]);
    }
}

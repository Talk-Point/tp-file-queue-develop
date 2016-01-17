<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_class');
            $table->boolean('is_runned')->default(false);
            $table->dateTime('is_runned_at');
            $table->boolean('is_failure')->default(false);
            $table->dateTime('is_failure_at');
            $table->boolean('is_success')->default(false);
            $table->dateTime('is_success_at');
            $table->dateTime('next_run_at');
            $table->integer('taskable_id')->unsigned();
            $table->string('taskable_type');
            $table->text('failure_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}

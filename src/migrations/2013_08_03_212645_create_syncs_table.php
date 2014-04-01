<?php

use Illuminate\Database\Migrations\Migration;

class CreateSyncsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('syncs', function($table)
		{

			$table->increments('id');

			$table->integer('morph_id')->unsigned();
			$table->integer('morph_type')->unsigned();

			$table->string('entity')->nullable();
			$table->string('type', 60);
			$table->string('url')->nullable();
			$table->string('class');
			$table->string('status', 10)->default('fail'); // fail / success
			$table->text('response')->nullable();

			$table->timestamp('started_at');
			$table->timestamps();

			$table->engine = 'InnoDB';

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('syncs');
	}

}
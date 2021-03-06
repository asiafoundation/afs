<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionsAddDefaultQuestion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::table('questions', function(Blueprint $table)
    {
      $table->integer('is_default');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::table('questions', function(Blueprint $table)
    {
      $table->dropColumn('is_default');
    });
	}

}

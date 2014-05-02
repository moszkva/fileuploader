<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('file');
		
		Schema::create('file', function($table)
		{
			$table->engine = 'InnoDB';
			
			$table->increments('id');
			
			$table->string('name', 255);
			$table->string('path', 1000)->nullable();
			$table->string('mime_type', 255);
			$table->integer('size')->unsigned();
			$table->string('checksum', '255')->unique();
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
		Schema::drop('file');
	}  

}

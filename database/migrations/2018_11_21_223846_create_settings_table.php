<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('option');
            $table->json('value');
            $table->string('type')->default('string');
            $table->timestamps();

            $table->primary('option');
        });

        DB::table('settings')->insert([
            ['option' => 'backups.enabled', 'value' => false, 'type' => 'boolean'],
            ['option' => 'backups.every', 'value' => 60, 'type' => 'integer'],
            ['option' => 'backups.max', 'value' => 5, 'type' => 'integer'],
            ['option' => 'restarts.enabled', 'value' => false, 'type' => 'boolean'],
            ['option' => 'restarts.time', 'value' => '00:00', 'type' => 'string'],
            ['option' => 'crash_fix.enabled', 'value' => false, 'type' => 'boolean'],
            ['option' => 'license', 'value' => '', 'type' => 'license'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}

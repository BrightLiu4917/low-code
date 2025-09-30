<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('low_code_database_sources', function (Blueprint $table) {
            $table->id();
            $table->string('disease_code', 32)->default('');
            $table->string('code', 32)->unique();
            $table->string('name', 64)->default('');
            $table->string('host', 64)->default('');
            $table->string('database', 64)->default('');
            $table->string('table', 64)->default('');
            $table->string('port', 64)->default('');
            $table->string('username', 255)->default('');
            $table->string('password', 255)->default('');
            $table->json('options')->nullable();
            $table->tinyInteger('source_type')->default(0);
            $table->unsignedBigInteger('creator_id')->default(0);
            $table->unsignedBigInteger('updater_id')->default(0);
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
            $table->index('disease_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('low_code_database_sources');
    }
};
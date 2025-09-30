<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('low_code_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->default('');
            $table->string('disease_code', 64)->default('');
            $table->string('code', 64)->unique();
            $table->string('org_code', 64)->default('');
            $table->tinyInteger('template_type')->default(0);
            $table->tinyInteger('content_type')->default(0);
            $table->string('description', 200)->default('');
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedBigInteger('creator_id')->default(0);
            $table->unsignedBigInteger('updater_id')->default(0);
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index('content_type');
            $table->index('disease_code');
            $table->index('org_code');
            $table->index('template_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('low_code_templates');
    }
};
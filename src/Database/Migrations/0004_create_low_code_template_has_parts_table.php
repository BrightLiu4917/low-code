<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('low_code_template_has_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_code', 64);
            $table->string('template_code', 64);
            $table->tinyInteger('locked')->default(0);
            $table->unsignedInteger('weight')->default(0);
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index('part_code');
            $table->index('template_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('low_code_template_has_parts');
    }
};
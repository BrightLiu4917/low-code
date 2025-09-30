<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('low_code_lists', function (Blueprint $table) {
            $table->id();
            $table->string('disease_code', 32)->default('');
            $table->string('code', 64)->unique();
            $table->string('parent_code', 64)->default('');
            $table->string('org_code', 64)->default('');
            $table->string('admin_name', 64)->default('');
            $table->string('family_doctor_name', 64)->default('');
            $table->string('mobile_doctor_name', 64)->default('');
            $table->unsignedInteger('admin_weight')->default(0);
            $table->unsignedInteger('family_doctor_weight')->default(0);
            $table->unsignedInteger('mobile_doctor_weight')->default(0);
            $table->string('crowd_type_code', 64)->default('');
            $table->string('template_code_filter', 64)->default('');
            $table->string('template_code_column', 64)->default('');
            $table->string('template_code_field', 64)->default('');
            $table->string('template_code_button', 64)->default('');
            $table->string('template_code_top_button', 64)->default('');
            $table->json('route_group')->nullable();
            $table->json('append_field_json')->nullable();
            $table->json('append_column_json')->nullable();
            $table->json('append_filter_json')->nullable();
            $table->json('append_button_json')->nullable();
            $table->json('append_top_button_json')->nullable();
            $table->json('remove_field_json')->nullable();
            $table->json('remove_filter_json')->nullable();
            $table->json('remove_column_json')->nullable();
            $table->json('remove_button_json')->nullable();
            $table->json('remove_top_button_json')->nullable();
            $table->json('default_order_by_json')->nullable();
            $table->tinyInteger('list_type')->default(0);
            $table->unsignedBigInteger('creator_id')->default(0);
            $table->unsignedBigInteger('updater_id')->default(0);
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index('disease_code');
            $table->index('org_code');
            $table->index('crowd_type_code');
            $table->index('parent_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('low_code_lists');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('task_attachments', function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('task_id');
$table->string('path');
$table->string('original_name');
$table->string('mime_type')->nullable();
$table->unsignedInteger('size')->nullable();
$table->timestamps();


$table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};

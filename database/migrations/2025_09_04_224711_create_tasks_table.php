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
        Schema::create('tasks', function (Blueprint $table) {
$table->id();
$table->string('title');
$table->text('description')->nullable();
$table->enum('status', ['new','in_progress','blocked','done','archived'])->default('new')->index();
$table->enum('priority', ['low','medium','high','urgent'])->default('medium')->index();
$table->unsignedBigInteger('creator_id');
$table->unsignedBigInteger('assignee_id')->nullable();
$table->date('due_date')->nullable();
$table->unsignedTinyInteger('progress')->default(0); // 0..100
$table->timestamps();


$table->foreign('creator_id')->references('id')->on('users')->cascadeOnDelete();
$table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

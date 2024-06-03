<?php

use Exonos\Mailapi\Models\Batch;
use Exonos\Mailapi\Models\Mail;
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
        Schema::create('mail_api', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->foreign('batch_id')
                ->references('id')
                ->on('mail_batches')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->json('variables')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('text')->nullable();
            $table->text('html')->nullable();
            $table->string('status')->default(Mail::STATUS_POSTED);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_api');
    }
};

<?php

use Exonos\Mailapi\Models\Batch;
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
        Schema::create('mail_batches', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code');
            $table->string('from');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->text('text')->nullable();
            $table->text('html')->nullable();
            $table->text('subject')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default(Batch::STATUS_UNCOMPLETE);
            $table->integer('recipient_count');
            $table->integer('pending_mail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_batches');
    }
};

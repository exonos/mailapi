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
        Schema::create('client_mail_api', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->string('secret', 100)->nullable();
            $table->boolean('revoked')->default(false);
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

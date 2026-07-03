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
        Schema::create('sh_transaction_receivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('sh_transactions');

            $table->string('name');
            $table->string('email');
            $table->string('language');
            $table->string('message');
            $table->string('reference')->nullable();
            $table->text('sign_url')->nullable();
            $table->string('created_date_time')->nullable();
            $table->string('modified_date_time')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sh_transaction_receivers');
    }
};

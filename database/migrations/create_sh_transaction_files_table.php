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
        Schema::create('sh_transaction_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('transaction_id')->constrained('sh_transactions');

            $table->text('display_name');
            $table->text('original_file_path')->nullable();
            $table->text('signed_file_path')->nullable();

            $table->longText('meta_data')->nullable();

            $table->boolean('meta_data_exported')->default(false);
            $table->boolean('content_exported')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sh_transaction_files');
    }
};

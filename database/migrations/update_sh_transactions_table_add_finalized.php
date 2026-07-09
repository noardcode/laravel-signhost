<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('sh_transactions', function (Blueprint $table) {
            $table->boolean('finalized')->default(false)->after('webhook_response');
            $table->dateTime('finalized_at')->nullable()->after('finalized');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('sh_transactions', function (Blueprint $table) {
            $table->dropColumn(['finalized', 'finalized_at']);
        });
    }
};

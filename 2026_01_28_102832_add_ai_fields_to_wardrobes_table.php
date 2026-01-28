<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('wardrobes', function (Blueprint $table) {
        $table->json('ai_analysis')->nullable()->after('notes');
        $table->boolean('ai_detected')->default(false)->after('ai_analysis');
        $table->decimal('ai_confidence', 5, 2)->nullable()->after('ai_detected');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('wardrobes', function (Blueprint $table) {
        $table->dropColumn(['ai_analysis', 'ai_detected', 'ai_confidence']);
    });
}
};

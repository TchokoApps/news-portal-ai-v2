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
        Schema::table('languages', function (Blueprint $table) {
            $table->string('lang')->nullable()->after('code')->comment('ISO language code');
            $table->string('slug')->nullable()->unique()->after('name')->comment('Language slug');
            $table->boolean('default')->default(false)->after('slug')->comment('Is default language');
            $table->boolean('status')->default(true)->after('default')->comment('Active/Inactive status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropColumn(['lang', 'slug', 'default', 'status']);
        });
    }
};

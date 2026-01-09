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
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('is_camera_enabled')->default(true)->after('user_id');
        $table->boolean('is_screenshot_enabled')->default(true)->after('is_camera_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // ロールバック（取り消し）した時にカラムを削除するように設定
        $table->dropColumn(['is_camera_enabled', 'is_screenshot_enabled']);
        });
    }
};

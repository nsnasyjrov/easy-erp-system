<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->dropColumn("phone_number", "telegram", "country", "region");
            $table->string('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->string('phone_number');
            $table->string('telegram')->nullable();
            $table->string('country');
            $table->string('region')->nullable();
            $table->dropColumn('type');
        });
    }
};

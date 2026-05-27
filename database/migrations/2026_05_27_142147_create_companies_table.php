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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('legal_address')->nullable();
            $table->string('registration_country')->nullable();
            $table->foreignId('chief_manager')->constrained('users')->nullable();
            $table->string('tin_number')->nullable()->unique();

            $table->foreignId('client_id')
                ->unique()
                ->constrained('clients')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

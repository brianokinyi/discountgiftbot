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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
		    $table->string('capital', 255)->nullable();
		    $table->string('citizenship', 255)->nullable();
		    $table->char('country_code', 3)->default('');
		    $table->string('currency', 255)->nullable();
		    $table->string('currency_code', 255)->nullable();
		    $table->string('currency_sub_unit', 255)->nullable();
            $table->string('currency_symbol', 3)->nullable();
            $table->integer('currency_decimals')->nullable();
		    $table->string('full_name', 255)->nullable();
		    $table->char('iso_3166_2', 2)->default('');
		    $table->char('iso_3166_3', 3)->default('');
		    $table->string('name', 255)->default('');
		    $table->char('region_code', 3)->default('');
		    $table->char('sub_region_code', 3)->default('');
		    $table->boolean('eea')->default(0);
		    $table->string('calling_code', 3)->nullable();
		    $table->string('flag', 6)->nullable();
            $table->unsignedInteger('position')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

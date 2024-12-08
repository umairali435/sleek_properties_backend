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
        Schema::create('properties', function (Blueprint $table) {
            $table->id(); 
            $table->string('title'); 
            $table->string('address'); 
            $table->text('description'); 
            $table->decimal('price', 10, 2); 
            $table->integer('bedrooms'); 
            $table->integer('bathrooms'); 
            $table->integer('sqft')->nullable(); 
            $table->string('property_type'); 
            $table->string('status')->default('available'); 
            $table->string('image'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};

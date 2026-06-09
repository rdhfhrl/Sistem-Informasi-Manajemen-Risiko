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
        Schema::create('risk', function (Blueprint $table) {
            $table->id('risk_id');
            $table->string('risk_code')->unique(); // Format: RISK-001
            $table->unsignedBigInteger('risk_pro_id');
            $table->unsignedBigInteger('risk_organization_id'); //Foreign Key to organizations table 
            $table->unsignedBigInteger('risk_strategic_objective_id'); //Foreign Key Strategic Objective table
            $table->unsignedBigInteger('risk_business_process_id'); // Foreign Key Business Process table
            $table->unsignedBigInteger('risk_category_id'); // Foreign Key Risk Categories table
            $table->string('risk_description');
            $table->unsignedBigInteger('risk_user_id');
            $table->timestamps();

            //Foreign Key
            $table->foreign('risk_pro_id')->references('pro_id')->on('project')->onDelete('cascade');
            $table->foreign('risk_organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
            $table->foreign('risk_strategic_objective_id')->references('strategic_objective_id')->on('strategic_objectives')->onDelete('cascade');
            $table->foreign('risk_business_process_id')->references('business_process_id')->on('business_processes')->onDelete('cascade');
            $table->foreign('risk_category_id')->references('risk_category_id')->on('risk_categories')->onDelete('cascade');
            $table->foreign('risk_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk');
    } 
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('applicant_name')->nullable()->after('job_id');
            $table->date('date_of_birth')->nullable()->after('applicant_name');
            $table->unsignedSmallInteger('age')->nullable()->after('date_of_birth');
            $table->string('residence')->nullable()->after('age');
            $table->text('skills')->nullable()->after('residence');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('cv_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['applicant_name', 'date_of_birth', 'age', 'residence', 'skills']);
        });
    }
};

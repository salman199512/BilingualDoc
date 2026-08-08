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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('html_content')->nullable();
            $table->string('page_size')->default('A4');
            $table->string('orientation')->default('portrait');
            $table->string('font_gujarati')->default('Noto Sans Gujarati');
            $table->string('font_english')->default('Times New Roman');
            $table->integer('margin_left')->default(40); // in mm (40mm = 4cm)
            $table->integer('margin_right')->default(40);
            $table->integer('margin_top')->default(20);
            $table->integer('margin_bottom')->default(20);
            $table->string('status')->default('draft'); // draft, final
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->longText('html_content')->nullable();
            $table->integer('version_number');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('html_content')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->string('field_key');
            $table->string('field_label');
            $table->string('field_type')->default('text'); // text, date, textarea, number
            $table->string('default_value')->nullable();
            $table->timestamps();
        });

        Schema::create('template_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('field_values');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_submissions');
        Schema::dropIfExists('template_fields');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};

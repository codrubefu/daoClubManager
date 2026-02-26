<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('rrule');
            $table->timestamps();

            $table->index(['club_id', 'name']);
            $table->index(['club_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_templates');
    }
};

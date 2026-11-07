<?php

declare(strict_types=1);

use App\Enums\GuideType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kullanıcı rolü, kategoriler, rehberler, CHM başlık ağacı (sections) ve medya tablolarını oluşturur.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 20)->default('user')->after('password')->index();
            });
        }

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('description', 500)->nullable();
            $table->string('icon', 60)->nullable();
            $table->unsignedSmallInteger('order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 30)->default(GuideType::HowTo->value)->index();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('max_depth')->default(3);
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'status', 'type']);
        });

        Schema::create('sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_id')->constrained('guides')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('sections')->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220);
            $table->unsignedInteger('order')->default(0);
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['guide_id', 'slug']);
            $table->index(['guide_id', 'parent_id', 'order']);
        });

        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('guide_id')->nullable()->constrained('guides')->nullOnDelete();
            $table->string('path', 500);
            $table->string('original_name', 255);
            $table->string('mime', 100);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    /**
     * Tabloları ve sütunları geri alır.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('guides');
        Schema::dropIfExists('categories');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }
};
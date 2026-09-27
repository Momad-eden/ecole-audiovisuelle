<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Impact Live : lieux (campus, studio, centre culturel), services, matériel à louer, packs,
 * agenda et demandes de devis ou de réservation. Plus le campus choisi par le candidat
 * et l'origine des réalisations (école ou studio).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('kind', 20);
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('map_url', 500)->nullable();
            $table->string('opening_hours')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('activity', 20);
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary', 300)->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('price_from')->nullable();
            $table->string('price_unit', 20)->nullable();
            $table->string('icon', 40)->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary', 300)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->string('usage', 20)->default('rental');
            $table->string('summary', 300)->nullable();
            $table->longText('description')->nullable();
            $table->json('specs')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedBigInteger('price_from')->nullable();
            $table->string('price_unit', 20)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['usage', 'status']);
        });

        Schema::create('rental_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary', 300)->nullable();
            $table->string('capacity')->nullable();
            $table->json('contents')->nullable();
            $table->unsignedBigInteger('price_from')->nullable();
            $table->string('price_unit', 20)->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('agenda_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('activity', 20);
            $table->foreignId('place_id')->nullable()->constrained()->nullOnDelete();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('summary', 300)->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('ticket_url', 500)->nullable();
            $table->boolean('is_reference')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'starts_at']);
        });

        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('type', 30);
            $table->string('status', 20)->default('new');
            $table->string('name', 150);
            $table->string('organization', 150)->nullable();
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('attendees')->nullable();
            $table->text('message')->nullable();
            $table->json('items')->nullable();
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('quoted_amount')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('booking_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::table('artworks', function (Blueprint $table) {
            $table->string('origin', 10)->default('school')->after('kind');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('place_id')->nullable()->after('offering_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('place_id');
        });
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('origin');
        });

        foreach (['booking_request_logs', 'booking_requests', 'agenda_events', 'rental_packs', 'equipment_items', 'equipment_categories', 'services', 'places'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

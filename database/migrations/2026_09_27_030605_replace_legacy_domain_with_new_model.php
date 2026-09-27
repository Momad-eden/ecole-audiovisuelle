<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nouveau modèle de données (aucune donnée de production à reprendre).
 *
 * Formation : filières → programmes → sessions → offres.
 * Scolarité : candidatures → étudiants → inscriptions → caisse.
 * Musée : salles, expositions, œuvres. Contenu : pages à blocs, FAQ, menus, messages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('galleries');

        // --- Référentiel pédagogique -------------------------------------------------

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('intro')->nullable();
            $table->string('accent_color', 7)->default('#F5B83D');
            $table->string('cover_image')->nullable();
            $table->string('cover_alt')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('skills')->nullable();
            $table->json('outcomes')->nullable();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('audience');
            $table->string('kind');
            $table->string('level_label')->nullable();
            $table->string('duration_label')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('skills')->nullable();
            $table->json('outcomes')->nullable();
            $table->json('prerequisites')->nullable();
            $table->json('equipment')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_alt')->nullable();
            $table->json('seo')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->dateTime('applications_open_at')->nullable();
            $table->dateTime('applications_close_at')->nullable();
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->restrictOnDelete();
            $table->foreignId('track_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedBigInteger('fee_amount')->default(0);
            $table->unsignedBigInteger('registration_fee_amount')->default(0);
            $table->string('funding_mode')->default('paid');
            $table->string('funding_note')->nullable();
            $table->boolean('is_open')->default(true);
            $table->timestamps();
            $table->unique(['cohort_id', 'track_id']);
        });

        // --- Candidatures et scolarité ----------------------------------------------

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reference')->unique();
            $table->foreignId('offering_id')->constrained()->restrictOnDelete();
            $table->string('audience');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->json('guardian')->nullable();
            $table->json('education')->nullable();
            $table->json('experience')->nullable();
            $table->json('documents')->nullable();
            $table->text('motivation')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('status')->default('submitted');
            $table->string('source')->default('online');
            $table->dateTime('interview_at')->nullable();
            $table->string('interview_location')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_version')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'submitted_at']);
        });

        Schema::create('application_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('offering_id')->constrained()->restrictOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->date('enrolled_on');
            $table->string('status')->default('enrolled');
            $table->unsignedBigInteger('fee_amount_due')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->string('funding_mode')->default('paid');
            $table->timestamp('certificate_issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['student_id', 'offering_id']);
        });

        // --- Caisse -------------------------------------------------------------------

        Schema::create('cash_closings', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end')->unique();
            $table->bigInteger('opening_balance');
            $table->unsignedBigInteger('total_in');
            $table->unsignedBigInteger('total_out');
            $table->bigInteger('closing_balance');
            $table->bigInteger('counted_cash')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('direction');
            $table->string('category');
            $table->unsignedBigInteger('amount');
            $table->string('method');
            $table->string('external_reference')->nullable();
            $table->date('occurred_on');
            $table->foreignId('enrollment_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('payee')->nullable();
            $table->string('label');
            $table->text('notes')->nullable();
            $table->foreignId('reverses_id')->nullable()->unique()->constrained('cash_transactions')->restrictOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['occurred_on', 'direction']);
        });

        // --- Musée --------------------------------------------------------------------

        Schema::create('exhibitions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('venue')->nullable();
            $table->longText('curatorial_text')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_alt')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('track_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind');
            $table->string('summary', 300)->nullable();
            $table->longText('creation_story')->nullable();
            $table->json('equipment')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_alt')->nullable();
            $table->json('gallery')->nullable();
            $table->string('audio_file')->nullable();
            $table->json('audio_peaks')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('transcript')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('artwork_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('person_name');
            $table->string('role');
            $table->unsignedInteger('position')->default(0);
        });

        Schema::create('artwork_exhibition', function (Blueprint $table) {
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exhibition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->primary(['artwork_id', 'exhibition_id']);
        });

        // --- Contenu du site ---------------------------------------------------------

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('free');
            $table->json('blocks')->nullable();
            $table->json('draft_blocks')->nullable();
            $table->json('seo')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->json('blocks')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location')->default('main');
            $table->string('label');
            $table->string('url');
            $table->boolean('is_button')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->timestamps();
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->string('category')->default('institutional')->after('name');
            $table->unsignedInteger('position')->default(0)->after('is_active');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('opening_hours')->nullable()->after('address');
            $table->string('map_url')->nullable()->after('opening_hours');
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['opening_hours', 'map_url', 'seo_title', 'seo_description']);
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['category', 'position']);
        });

        foreach ([
            'redirects', 'contact_messages', 'faqs', 'menu_items', 'page_revisions', 'pages',
            'artwork_exhibition', 'artwork_credits', 'artworks', 'exhibitions',
            'cash_transactions', 'cash_closings', 'enrollments', 'application_events', 'applications',
            'students', 'offerings', 'cohorts', 'programs', 'tracks', 'rooms',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        // Anciennes tables, dans leur dernier état.
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('level')->nullable();
            $table->unsignedInteger('students_count')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['Homme', 'Femme']);
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->default('Sénégalaise');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->date('registration_date');
            $table->enum('status', ['Inscrit', 'Diplômé', 'Suspendu', 'Abandonné'])->default('Inscrit');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('address')->nullable();
            $table->string('last_diploma')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('academic_field')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('volet')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('inflow');
            $table->string('category', 50)->nullable();
            $table->string('title')->nullable();
            $table->foreignId('student_id')->nullable()->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->string('reference')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('image');
            $table->string('file_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};

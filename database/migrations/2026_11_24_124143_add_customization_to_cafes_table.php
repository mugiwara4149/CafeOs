<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->string('tagline')->nullable()->after('slug');
            $table->text('description')->nullable()->after('tagline');
            $table->string('logo_path')->nullable()->after('description');
            $table->string('banner_path')->nullable()->after('logo_path');

            // No-code website theme defaults (matching CafeOS Navy & Gold)
            $table->string('primary_color', 7)->default('#09315a')->after('banner_path');
            $table->string('secondary_color', 7)->default('#0f4881')->after('primary_color');
            $table->string('accent_color', 7)->default('#c1a88b')->after('secondary_color');
            $table->string('font_family', 100)->default('Georgia')->after('accent_color');

            // Public contact details
            $table->string('contact_phone', 20)->nullable()->after('font_family');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'description',
                'logo_path',
                'banner_path',
                'primary_color',
                'secondary_color',
                'accent_color',
                'font_family',
                'contact_phone',
                'contact_email',
            ]);
        });
    }
};
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
	 DB::statement('ALTER TABLE oauth_clients ALTER COLUMN id SET DATA TYPE uuid USING id::text::uuid');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

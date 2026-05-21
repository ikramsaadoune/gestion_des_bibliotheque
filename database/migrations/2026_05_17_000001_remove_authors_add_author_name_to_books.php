<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('author_id');
        });

        DB::statement('UPDATE books SET author_name = (SELECT name FROM authors WHERE authors.id = books.author_id)');

        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['author_id']);
            $table->dropColumn('author_id');
        });

        Schema::dropIfExists('authors');
    }

    public function down(): void
    {
        //
    }
};

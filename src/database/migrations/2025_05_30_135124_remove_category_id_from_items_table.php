<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCategoryIdFromItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'category_id')) {
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
            }
        });
    }
}

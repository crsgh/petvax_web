<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Mongo documents are schemaless; soft-delete just writes a
        // deleted_at field on save, no column needs to be predefined.
    }

    public function down()
    {
        //
    }
};
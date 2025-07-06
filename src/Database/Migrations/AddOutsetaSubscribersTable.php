<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Database\Migration;

class AddOutsetaSubscribersTable extends Migration
{
    public function up(): void
    {
        // Use Schema::create to make a new table
        $this->schema->create('outseta_subscribers', function (Blueprint $table) {
            // Standard auto-incrementing primary key
            $table->increments('id');

            // Foreign key to link to the main users table
            $table->integer('user_id')->unsigned()->unique();

            // The UID from Outseta. This should be unique as well.
            $table->string('outseta_uid')->unique();
            
            // Standard created_at and updated_at columns
            $table->timestamps();

            // Define the foreign key relationship.
            // This ensures data integrity and automatically deletes the subscriber
            // record if the corresponding user is deleted.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // The reverse operation of create is to drop the table.
        $this->schema->dropIfExists('outseta_subscribers');
    }
}
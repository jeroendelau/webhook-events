<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')
            ->nullable()
            ->constrained('webhooks')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->string('topic');
            $table->json('payload');
            $table->timestamp('last_attempt');
            $table->boolean('success')->default(false);
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_dispatches');
    }
}

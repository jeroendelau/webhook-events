<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookDeliveryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_event_id')
            ->nullable()
            ->constrained('webhook_dispatches')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->string('response_status');
            $table->text('response_message');
            $table->timestamp('sent_at');
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
        Schema::dropIfExists('webhook_delivery_logs');
    }
}

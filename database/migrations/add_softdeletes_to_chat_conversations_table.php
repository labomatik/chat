<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Musonza\Chat\ConfigurationManager;

class AddSoftdeletesToChatConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(ConfigurationManager::CONVERSATIONS_TABLE, function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(ConfigurationManager::CONVERSATIONS_TABLE, function (Blueprint $table) {
           $table->dropSoftDeletes();
        });
    }
}

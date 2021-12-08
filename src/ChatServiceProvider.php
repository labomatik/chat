<?php

namespace Musonza\Chat;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishMigrations();
        $this->publishConfig();

        if (config('musonza_chat.should_load_routes')) {
            require __DIR__.'/Http/routes.php';
        }
    }

    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerChat();
    }

    /**
     * Registers Chat.
     *
     * @return void
     */
    private function registerChat()
    {
        $this->app->bind('\Musonza\Chat\Chat', function () {
            return $this->app->make(Chat::class);
        });
    }

    /**
     * Publish package's migrations.
     *
     * @return void
     */
    public function publishMigrations()
    {
        $timestamp = date('Y_m_d_His', time() - 500); // Make sure it's first
        $stub = __DIR__.'/../database/migrations/create_chat_tables.php';
        $target = $this->app->databasePath().'/migrations/'.$timestamp.'_create_chat_tables.php';

        $timestamp = date('Y_m_d_His', time());
        $stub_softdeletes = __DIR__.'/../database/migrations/add_softdeletes_to_chat_conversations_table.php';
        $target_softdeletes = $this->app->databasePath().'/migrations/'.$timestamp.'_add_softdeletes_to_chat_conversations_table.php';

        $timestamp = date('Y_m_d_His', time());
        $stub_participation = __DIR__.'/../database/migrations/add_softdeletes_to_chat_participation_table.php';
        $participation_softdeletes = $this->app->databasePath().'/migrations/'.$timestamp.'_add_softdeletes_to_chat_participation_table.php';

        $this->publishes([
            $stub => $target,
            $stub_softdeletes => $target_softdeletes,
            $stub_participation => $participation_softdeletes
            ], 'chat.migrations');
    }

    /**
     * Publish package's config file.
     *
     * @return void
     */
    public function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'chat.config');
    }
}

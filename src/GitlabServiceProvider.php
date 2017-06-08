<?php

namespace ServiceMap\Gitlab;

use Gitlab\Client;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Support\ServiceProvider;

class GitlabServiceProvider extends ServiceProvider
{
  /**
   * Boot the service provider.
   *
   * @return void
   */
  public function boot()
  {
    $this->setupConfig();
  }

  /**
   * Setup the config.
   *
   * @return void
   */
  protected function setupConfig()
  {
    $source = realpath(__DIR__.'/../config/gitlab.php');

    if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
      $this->publishes([$source => config_path('gitlab.php')]);
    } elseif ($this->app instanceof LumenApplication) {
      $this->app->configure('gitlab');
    }

    $this->mergeConfigFrom($source, 'gitlab');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->registerFactory();
    $this->registerManager();
    $this->registerBindings();
  }

  /**
   * Register the factory class.
   *
   * @return void
   */
  protected function registerFactory()
  {
    $this->app->singleton('gitlab.factory', function () {
      return new GitlabFactory();
    });

    $this->app->alias('gitlab.factory', GitlabFactory::class);
  }

  /**
   * Register the manager class.
   *
   * @return void
   */
  protected function registerManager()
  {
    $this->app->singleton('gitlab', function (Container $app) {
      $config = $app['config'];
      $factory = $app['gitlab.factory'];

      return new GitlabManager($config, $factory);
    });

    $this->app->alias('gitlab', GitlabManager::class);
  }

  /**
   * Register the bindings.
   *
   * @return void
   */
  protected function registerBindings()
  {
    $this->app->bind('gitlab.connection', function (Container $app) {
      $manager = $app['gitlab'];

      return $manager->connection();
    });

    $this->app->alias('gitlab.connection', Client::class);
  }

  /**
   * Get the services provided by the provider.
   *
   * @return string[]
   */
  public function provides()
  {
    return [
      'gitlab',
      'gitlab.factory',
      'gitlab.connection',
    ];
  }
}

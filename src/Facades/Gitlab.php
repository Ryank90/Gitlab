<?php

namespace ServiceMap\Gitlab\Facades;

use Illuminate\Support\Facades\Facade;

class Gitlab extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return 'gitlab';
  }
}

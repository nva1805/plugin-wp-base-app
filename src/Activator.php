<?php

namespace WPBaseApp;

use WPBaseApp\Services\AppPageManager;

class Activator
{
  public static function activate(): void
  {
    AppPageManager::createEnabledPages();
  }
}

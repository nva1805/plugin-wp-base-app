<?php

namespace WPBaseApp;

use WPBaseApp\Support\AppPageManager;

class Activator
{
  public static function activate(): void
  {
    AppPageManager::createEnabledPages();
  }
}

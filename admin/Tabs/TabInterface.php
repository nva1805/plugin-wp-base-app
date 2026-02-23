<?php

namespace WPBaseApp\Admin\Tabs;

interface TabInterface
{
  /**
   * Get the unique ID (slug) of the tab
   */
  public function getId(): string;

  /**
   * Get the label/title of the tab for the navigation
   */
  public function getTitle(): string;

  /**
   * Get the dashicon class name for the tab
   */
  public function getIcon(): string;

  /**
   * Get the settings group name for this tab
   */
  public function getGroupName(): string;

  /**
   * Register the settings for this tab using the Settings API
   */
  public function registerSettings(): void;

  /**
   * Render the tab content (include the view file)
   */
  public function render(): void;
}

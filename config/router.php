<?php

return [
  'logina' => [
    'title' => 'Login',
    'slug' => 'logina',
    'template' => '/pages/login.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\LoginController',
  ],
  'register' => [
    'title' => 'Register',
    'slug' => 'register',
    'template' => '/pages/register.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\RegistenController',
  ],
  'profile' => [
    'title' => 'Profile',
    'slug' => 'profile',
    'template' => '/pages/profile.html.twig',
    'controller' => '\WPBaseApp\Controllers\User\ProfileController',
  ],
];

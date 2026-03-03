<?php

return [
  'login' => [
    'title' => 'Login',
    'slug' => 'login',
    'template' => '/pages/login.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\LoginController',
    'auth' => 'guest',
  ],
  'register' => [
    'title' => 'Register',
    'slug' => 'register',
    'template' => '/pages/register.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\RegisterController',
    'auth' => 'guest',
  ],
  'profile' => [
    'title' => 'Profile',
    'slug' => 'profile',
    'template' => '/pages/profile.html.twig',
    'controller' => '\WPBaseApp\Controllers\User\ProfileController',
    'auth' => 'authenticated',
  ],
];

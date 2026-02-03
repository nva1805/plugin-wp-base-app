<?php

return [
  'login' => [
    'title' => 'Login',
    'slug' => 'login',
    'template' => '/pages/login.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\LoginController',
    'preventRedirect' => true,
  ],
  'register' => [
    'title' => 'Register',
    'slug' => 'register',
    'template' => '/pages/register.html.twig',
    'controller' => '\WPBaseApp\Controllers\Auth\RegisterController',
    'preventRedirect' => true,
  ],
  'profile' => [
    'title' => 'Profile',
    'slug' => 'profile',
    'template' => '/pages/profile.html.twig',
    'controller' => '\WPBaseApp\Controllers\User\ProfileController',
  ],
];

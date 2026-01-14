# WP Base App

A professional WordPress plugin starter with modern architecture and best practices.

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Run `composer install` (optional, if using dependencies)
3. Activate the plugin through 'Plugins' menu in WordPress

## Structure

```
my-plugin/
│
├── my-plugin.php
├── autoload.php
├── bootstrap.php
│
├── config/
│   ├── page-list.php
│   ├── routes.php
│   └── services.php            <-- cấu hình DI container nhẹ nhàng
│
├── src/
│   ├── Router/
│   │   ├── Route.php
│   │   ├── RouteRegistrar.php
│   │   ├── RouteDispatcher.php
│   │   └── Router.php
│   │
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   └── Route/
│   │       └── LoginController.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── UserService.php
│   │   └── TwigService.php
│   │
│   ├── Repositories/
│   │   └── UserRepository.php
│   │
│   ├── Http/
│   │   ├── Request.php
│   │   └── Response.php
│   │
│   └── Helpers/
│       └── helpers.php
│
├── templates/
│   ├── base.twig
│   └── routes/
│       └── login.twig
│
└── assets/
    ├── css/
    └── js/

## License

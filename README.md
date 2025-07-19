# Sager

Sager is a lightweight and modern web application designed to simplify user registration, role management, and secure authentication. Built using Laravel and Laravel Fortify, it offers a clean structure and extensible foundation for building user-based platforms.

## Features

✅ User registration and login  
✅ Role-based access control (e.g., seller, buyer)  
✅ Secure password handling  
✅ Customizable frontend templates  
✅ Built-in validation and error handling

## Technologies Used

-   PHP 8+
-   Laravel 10
-   Laravel Fortify
-   Vue.js
-   MySQL (for data storage)

## Installation

1. Clone the repository:

```bash
git clone git@github.com:Rachad-Alabi-ADEKAMBI/sager.git
cd sager
```

2. Install dependencies:

```bash
composer install
npm install && npm run dev
```

3. Copy `.env` and set your environment values:

```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations:

```bash
php artisan migrate
```

5. Launch the server:

```bash
php artisan serve
```

## To Do

-   [ ] Email verification system
-   [ ] User dashboard
-   [ ] Profile editing feature
-   [ ] API endpoints for external access
-   [ ] Responsive mobile design

## Author

[Rachad Alabi ADEKAMBI](https://github.com/Rachad-Alabi-ADEKAMBI)  
Self-taught Laravel developer from Benin, passionate about clean code and modern web technologies.

---

Feel free to fork or contribute. PRs are welcome.

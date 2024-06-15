# Study Portal Project

A comprehensive study portal where users can enroll in courses, watch lessons, write comments, and earn achievements. The system sends notifications via email for unlocked achievements and handles notifications asynchronously.

## Features

- **User Registration & Authentication**: Users can register and log in using Sanctum for token-based authentication.
- **Courses & Lessons**: Users can enroll in courses and watch lessons.
- **Comments**: Users can comment on lessons.
- **Achievements**: Users can unlock achievements based on their interactions with lessons and comments.
- **Badges**: Users earn badges based on the number of achievements unlocked.
- **Email Notifications**: Users receive email notifications when they unlock achievements.

## Requirements

- PHP 8.x
- Composer
- MySQL or any other supported database
- Laravel 8.x
- Node.js (for compiling frontend assets, if needed)

## Installation

1. **Clone the repository**:

    ```sh
    git clone https://github.com/AhmadKholy10/study-portal.git
    cd study-portal
    ```

2. **Install dependencies**:

    ```sh
    composer install
    npm install
    npm run dev
    ```


3. **Database setup**:

    Update your database configuration in the `.env` file:

    ```sh
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=study_portal_db
    DB_USERNAME=root
    DB_PASSWORD=yourpassword
    ```

   ```sh
    php artisan migrate --seed
    ```
5. **Run the development server**:
    I used xampp for running apache and mysql servers to run the application
   
    ```sh
    php artisan serve
    ```

## API Endpoints

### Authentication

- **Register**: `POST /api/register`
- **Login**: `POST /api/login`
- **Logout**: `POST /api/logout`

### Courses

- **List all courses**: `GET /api/courses`
- **Enroll in a course**: `POST /api/courses/{course}/enroll`

### Lessons

- **Watch a lesson**: `POST /api/lessons/{lesson}/watch`

### Comments

- **Add a comment to a lesson**: `POST /api/lessons/{lesson}/comments`

### Achievements

- **Get user achievements**: `GET /api/users/{user}/achievements`

## Testing

To run the tests, use the following command:

```sh
php artisan test tests/Feature/AchievementTest.php
```

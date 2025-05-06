# Hotel Management System

A simple hotel management system built with PHP, MySQL, HTML, CSS (Bootstrap), and JavaScript. This web application allows users to browse hotels, make bookings, and provides an admin panel for managing hotels, users, and bookings.

## Features

### User Side
- **Authentication**: Register and login functionality with password encryption
- **Browse Hotels**: View all hotels with filtering options (location, price range, rating)
- **Hotel Details**: View detailed information about specific hotels
- **Bookings**: Book hotels with date selection and price calculation
- **Favorites**: Save hotels to favorites for quick access
- **Calendar View**: See upcoming bookings in a calendar format
- **User Profile**: Update personal information and change password

### Admin Side
- **Dashboard**: Overview of system statistics (users, hotels, bookings, revenue)
- **User Management**: Create, update, and delete user accounts
- **Hotel Management**: Add, edit, and delete hotels with details
- **Booking Management**: View and manage all bookings with status updates
- **Admin Profile**: Update admin profile information and change password

## Project Structure

```
hotel-management/
├── admin/
│   ├── bookings.php
│   ├── dashboard.php
│   ├── hotel_process.php
│   ├── hotels.php
│   ├── includes/
│   │   ├── footer.php
│   │   └── header.php
│   ├── index.php
│   ├── profile.php
│   ├── user_process.php
│   └── users.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── images/
│   └── js/
│       └── script.js
├── config/
│   ├── config.php
│   └── db_connect.php
├── includes/
│   ├── footer.php
│   └── header.php
├── users/
│   ├── book.php
│   ├── bookings.php
│   ├── calendar.php
│   ├── cancel_booking.php
│   ├── favorites.php
│   ├── profile.php
│   └── toggle_favorite.php
├── hotel.php
├── hotels.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── README.md
└── sample_data.php
```

## Technologies Used

- **Backend**: PHP 8.0+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.1
- **Server**: XAMPP (Apache)

## Requirements

- XAMPP (or similar PHP development environment with Apache and MySQL)
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.4+
- Web browser (Chrome, Firefox, Edge, Safari, etc.)

## Installation & Setup

1. **Install XAMPP**:
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start Apache and MySQL services

2. **Create Project Directory**:
   - Navigate to your XAMPP htdocs folder (e.g., `C:/xampp/htdocs/`)
   - Create a new folder named `hotel-management`
   - Clone or extract all project files into this folder

3. **Configure Database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin/)
   - Create a new database named `hotel_management`
   - Import the database schema or run the SQL queries provided in database setup section

4. **Configure Database Connection**:
   - Open `config/db_connect.php`
   - Update the database credentials if needed (default should work with XAMPP)

5. **Add Sample Data (Optional)**:
   - Visit http://localhost/hotel-management/sample_data.php to populate the database with sample hotels and users

6. **Access the Website**:
   - Main site: http://localhost/hotel-management/
   - Admin panel: http://localhost/hotel-management/admin/

## Default Login Credentials

### Admin User
- **Email**: admin@example.com
- **Password**: admin123

### Regular User
- **Email**: user@example.com
- **Password**: user123

## Key Files and Their Functions

- **config/config.php**: Core configuration file with global settings and utility functions
- **config/db_connect.php**: Database connection settings
- **index.php**: Home page with featured hotels
- **hotels.php**: List of all hotels with filtering options
- **hotel.php**: Single hotel details page
- **login.php & register.php**: User authentication pages
- **admin/dashboard.php**: Admin dashboard with system overview
- **admin/hotels.php**: Admin hotel management page
- **admin/users.php**: Admin user management page
- **admin/bookings.php**: Admin booking management page
- **users/book.php**: Hotel booking page
- **users/favorites.php**: User's favorite hotels page
- **users/bookings.php**: User's booking list page
- **users/calendar.php**: Calendar view of user's bookings

## Future Enhancements

Potential features to add in the future:
1. Advanced search functionality
2. Rating and review system
3. Payment gateway integration
4. Email notifications for bookings
5. Multiple room types for hotels
6. Reports and analytics
7. Multi-language support

## Troubleshooting

- **Database Connection Issues**: Check your database credentials in `config/db_connect.php`
- **Permission Issues**: Ensure proper file permissions for the web server
- **404 Errors**: Make sure all files are in the correct locations and named properly
- **Session Issues**: Verify that PHP sessions are working correctly
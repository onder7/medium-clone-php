# Broomble Blog Platform

A simple blog platform built with PHP and MySQL.

## Features

- User authentication system
- Blog post creation and management
- Profile management
- Responsive design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/broomble.git
```

2. Create a MySQL database and import the schema
```bash
mysql -u root -p
CREATE DATABASE medium;
```

3. Copy the configuration template
```bash
cp config.example.php config.php
```

4. Edit the configuration file with your database credentials
```php
// config.php
$config['db'] = array(
    'host'     => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'dbname'   => 'medium'
);
```

5. Set up virtual host or point your web server to the project directory

6. Ensure write permissions for uploads directory
```bash
chmod 755 images/profile
```

## Database Schema

The project requires the following tables:
- users
- posts
- (other tables if any)

SQL schema is available in `database/schema.sql`

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
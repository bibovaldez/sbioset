# BiosecurityTech - Laravel Installation on Linux

## Prerequisites
- PHP (>= 8.0)
- Composer
- MySQL
- Git
- Apache

## Step 1: Update and Install Required Packages

1. **Update the package list and upgrade existing packages:**
   ```bash
   sudo apt update && sudo apt upgrade
    ```
2. **Install PHP and necessary extensions:**
    ```bash
    sudo apt install php php-cli php-fpm php-json php-common php-mysql php-zip php-gd php-mbstring php-curl php-xml php-pear php-bcmath unzip curl
     ```
3. **Install MySQL:**
    ```bash
    sudo apt install mysql-server
    ```
4. **Install Composer:**
    ```bash
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    ```
5. **Install Git:**
    ```bash
    sudo apt install git
    ```
6. **Install Apache:**
    ```bash
    sudo apt install apache2
    ```
7. **Install Node.js and NPM:**
    ```bash
    sudo apt install nodejs npm
    ```

## Step 2: Configure PHP Settings

1. **Edit the PHP configuration file:**
    ```bash
    sudo nano /etc/php/{version}/cli/php.ini
    ```
2. **Update the following settings:**
    ```ini
    memory_limit = 512M
    upload_max_filesize = 100M
    post_max_size = 100M
    max_execution_time = 600
    max_input_time = 600
    extension=mysqli
    extension=sodium
    extension=php_fileinfo.dll
    extension=pdo_mysql
    ```
3. **Restart the PHP service:**
    ```bash
    sudo systemctl restart apache2
    ```
4. **Check the PHP version:**
    ```bash
    php -v
    ```
5. **Check the PHP modules:**
    ```bash
    php -m
    ```
6. **Check the PHP configuration:**
    ```bash
    php -i
    ```

## Step 3: Clone the GitHub Project

1. **Navigate to your web root directory:**
    ```bash
    cd /var/www/html
    ```
2. **Clone the GitHub project:**
    ```bash
    sudo git clone https://github.com/bibovaldez/BiosecurityTech.git
    ```
3. **Navigate to the project directory:**
    ```bash
    cd BiosecurityTech
    ```
4. **Install the project dependencies:**
    ```bash
    composer install
    ```
5. **Install Laravel dependencies using Composer:**
    ```bash
    composer install
    ```
6. **Copy .env.example to .env:**
    ```bash
    cp .env.example .env
    ```
7. **Edit the .env file:**
    ```bash
    nano .env
    ```
8. **Update the following settings:**
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=
    DB_PORT=
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    DUMP_BINARY_PATH=/usr/bin
    ```
9. **Generate a new application key:**
    ```bash
    php artisan key:generate
    ```

## Step 4: Set File Permissions

1. **Change ownership of the project files:**
    ```bash
    sudo chown -R www-data:www-data /var/www/html/BiosecurityTech
    ```
2. **Change permissions of the project files:**
    ```bash
    sudo chmod -R 775 /var/www/html/BiosecurityTech/storage
    sudo chmod -R 775 /var/www/html/BiosecurityTech/bootstrap/cache
    ```

## Step 5: Configure the MySQL Database

1. **Login to MySQL:**
    ```bash
    mysql -u root -p
    ```
2. **Create a new database:**
    ```sql
    CREATE DATABASE biosetdb;
    ```
3. **Create a new user:**
    ```sql
    CREATE USER 'bioset'@'localhost' IDENTIFIED BY 'bioset@2024';
    GRANT ALL PRIVILEGES ON biosetdb.* TO 'bioset'@'localhost';
    FLUSH PRIVILEGES;
    ```
4. **Exit MySQL:**
    ```sql
    exit
    ```

## Step 6: Migrate and Seed the Database

1. **Migrate the database:**
    ```bash
    php artisan migrate
    ```
2. **Seed the database:**
    ```bash
    php artisan db:seed
    ```
3. **Clear the cache:**
    ```bash
    php artisan cache:clear
    ```
4. **Optimize the application:**
    ```bash
    php artisan optimize
    ```

## Step 7: Configure Firewall

1. **Allow HTTP and HTTPS traffic:**
    ```bash
    sudo ufw allow in "Apache Full"
    ```
2. **Enable the firewall:**
    ```bash
    sudo ufw enable
    ```
3. **Check the firewall status:**
    ```bash
    sudo ufw status
    ```

## Step 8: Set Up Apache Virtual Host

1. **Create a new Apache virtual host configuration:**
    ```bash
    sudo nano /etc/apache2/sites-available/biosecuritytech.conf
    ```
2. **Add the following configuration:**
    ```apache
<VirtualHost *:80>
    ServerAdmin admin@yourdomain.com
    ServerName 192.168.1.132
    DocumentRoot /var/www/html/BiosecurityTech/public

    <Directory /var/www/html/BiosecurityTech>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
    ```
3. **Enable the virtual host:**
    ```bash
    sudo a2ensite biosecuritytech.conf
    sudo a2enmod rewrite
    ```
4. **Restart the Apache service:**
    ```bash
    sudo systemctl restart apache2
    ```



# BiosecurityTech - Laravel Installation on Linux (Updated)

## Prerequisites
- PHP (>= 8.1)
- Composer
- MySQL
- Git
- Apache
- Node.js and NPM

## Step 1: Update and Install Required Packages

1. **Update the package list and upgrade existing packages:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. **Install PHP and necessary extensions:**
   ```bash
   sudo apt install -y php php-cli php-fpm php-json php-common php-mysql php-zip php-gd php-mbstring php-curl php-xml php-bcmath unzip
   ```

3. **Install MySQL:**
   ```bash
   sudo apt-get install -y mariadb-server 
   ```

4. **Install Composer:**
   ```bash
   curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
   ```

5. **Install Git:**
   ```bash
   sudo apt install -y git
   ```

6. **Install Apache:**
   ```bash
   sudo apt install -y apache2
   ```

7. **Install Node.js and NPM:**
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
   sudo apt install -y nodejs
   ```

## Step 2: Configure PHP Settings

1. **Edit the PHP configuration file:**
   ```bash
    sudo apt install libsodium-dev libsodium23
   sudo nano /etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/apache2/php.ini
   ```

2. **Update the following settings:**
   ```ini
   memory_limit = 512M
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 600
   max_input_time = 600
   ```

3. **Restart the Apache service:**
   ```bash
   sudo systemctl restart apache2
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

5. **Copy .env.example to .env:**
   ```bash
   cp .env.example .env
   ```

6. **Generate a new application key:**
   ```bash
   php artisan key:generate
   ```

7. **Edit the .env file:**
   ```bash
   nano .env
   ```
   Update the database settings and other necessary configurations.

8. **Install NPM dependencies and compile assets:**
   ```bash
   npm install
   npm run build
   ```
9. ** Clear the cache and optimize:**
   ```bash
   php artisan config:cache
   php artisan config:clear
   php artisan optimize:clear
   php artisan cache:clear
   php artisan config:cache
   php artisan optimize
   php artisan storage:link
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

1. **Secure MySQL installation:**
   ```bash
   sudo mysql_secure_installation
   ```

2. **Login to MySQL:**
   ```bash
   sudo mysql
   ```

3. **Create a new database and user:**
   ```sql
   CREATE DATABASE biosetdb;
   CREATE USER 'bioset'@'localhost' IDENTIFIED BY 'bioset@2024';
   GRANT ALL PRIVILEGES ON biosetdb.* TO 'bioset'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
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

3. **Clear the cache and optimize:**
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

## Step 7: Configure Apache Virtual Host

1. **Create a new Apache virtual host configuration:**
   ```bash
   sudo nano /etc/apache2/sites-available/biosecuritytech.conf
   ```

2. **Add the following configuration:**
   ```apache
   <VirtualHost *:80>
       ServerAdmin admin@yourdomain.com
       ServerName yourdomain.com
       DocumentRoot /var/www/html/BiosecurityTech/public

       <Directory /var/www/html/BiosecurityTech>
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/error.log
       CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>
   ```

3. **Enable the virtual host and rewrite module:**
   ```bash
   sudo a2ensite biosecuritytech.conf
   sudo a2enmod rewrite
   ```

4. **Restart the Apache service:**
   ```bash
   sudo systemctl restart apache2
   ```

## Step 8: Final Steps

1. **Set up scheduled tasks:**
   Add the following to your crontab (`sudo crontab -e`):
   ```
   * * * * * cd /var/www/html/BiosecurityTech && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Configure queue worker (if needed):**
   ```bash
   sudo nano /etc/supervisor/conf.d/laravel-worker.conf
   ```
   Add the following configuration:
   ```
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/html/BiosecurityTech/artisan queue:work
   autostart=true
   autorestart=true
   user=www-data
   numprocs=8
   redirect_stderr=true
   stdout_logfile=/var/www/html/BiosecurityTech/worker.log
   ```
   Then run:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start laravel-worker:*
   ```

3. **Set up SSL (optional but recommended):**
   Use Let's Encrypt for free SSL certificates:
   ```bash
   sudo apt install certbot python3-certbot-apache
   sudo certbot --apache -d yourdomain.com
   ```

4. **Final checks:**
   - Ensure all services are running: Apache, MySQL, PHP-FPM
   - Check Laravel logs for any errors: `tail -f /var/www/html/BiosecurityTech/storage/logs/laravel.log`
   - Test the application in a web browser

Remember to replace `yourdomain.com` with your actual domain or server IP address where appropriate.
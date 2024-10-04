# BiosecurityTech - Laravel Installation on Hostinger (Updated)

## Prerequisites
- PHP (>= 8.1)
- Composer
- MySQL
- Git
- Node.js and NPM

## Step 1: Install Project Dependencies

1. **Install Composer dependencies:**
   ```bash
   php ~/composer.phar install
   ```

2. **Copy .env.example to .env:**
   ```bash
   cp .env.example .env
   ```

3. **Generate a new application key:**
   ```bash
   php artisan key:generate
   ```

4. **Edit the .env file:**
   ```bash
   nano .env
   ```
   Update the database settings and other necessary configurations.

5. **Install NPM dependencies and compile assets:**
   ```bash
   npm install
   npm run build
   ```

6. **Clear the cache and optimize:**
   ```bash
   php artisan config:cache
   php artisan config:clear
   php artisan optimize:clear
   php artisan cache:clear
   php artisan config:cache
   php artisan optimize
   php artisan storage:link
   ```

## Step 2: Set File Permissions

1. **Change ownership of the project files:**
   ```bash
   sudo chown -R www-data:www-data /path/to/your/project
   ```

2. **Change permissions of the project files:**
   ```bash
   sudo chmod -R 775 /path/to/your/project/storage
   sudo chmod -R 775 /path/to/your/project/bootstrap/cache
   ```

## Step 3: Configure the MySQL Database

1. **Login to MySQL:**
   ```bash
   mysql -u your_username -p
   ```

2. **Create a new database and user:**
   ```sql
   CREATE DATABASE biosetdb;
   CREATE USER 'bioset'@'localhost' IDENTIFIED BY 'bioset@2024';
   GRANT ALL PRIVILEGES ON biosetdb.* TO 'bioset'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

## Step 4: Migrate and Seed the Database

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

## Step 5: Create Symbolic Link

1. **Create a symbolic link from [`public/`](command:_github.copilot.openRelativePath?%5B%7B%22scheme%22%3A%22file%22%2C%22authority%22%3A%22%22%2C%22path%22%3A%22%2FC%3A%2Fbisoet%2FBiosecurityTech%2Fpublic%2F%22%2C%22query%22%3A%22%22%2C%22fragment%22%3A%22%22%7D%5D "c:\bisoet\BiosecurityTech\public\") to `public_html`:**
   ```bash
   ln -s /path/to/your/project/public /path/to/your/project/public_html
   ```

## Step 6: Final Steps

1. **Set up scheduled tasks:**
   Add the following to your crontab ([`crontab -e`](command:_github.copilot.openSymbolFromReferences?%5B%22crontab%20-e%22%2C%5B%7B%22uri%22%3A%7B%22%24mid%22%3A1%2C%22fsPath%22%3A%22c%3A%5C%5Cbisoet%5C%5CBiosecurityTech%5C%5CREADME.md%22%2C%22_sep%22%3A1%2C%22external%22%3A%22file%3A%2F%2F%2Fc%253A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22path%22%3A%22%2FC%3A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22scheme%22%3A%22file%22%7D%2C%22pos%22%3A%7B%22line%22%3A214%2C%22character%22%3A29%7D%7D%5D%5D "Go to definition")):
   ```
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Configure queue worker (if needed):**
   ```bash
   sudo nano /etc/supervisor/conf.d/laravel-worker.conf
   ```
   Add the following configuration:
   ```
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /path/to/your/project/artisan queue:work
   autostart=true
   autorestart=true
   user=www-data
   numprocs=8
   redirect_stderr=true
   stdout_logfile=/path/to/your/project/worker.log
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
   - Ensure all services are running: PHP, MySQL
   - Check Laravel logs for any errors: [`tail -f /path/to/your/project/storage/logs/laravel.log`](command:_github.copilot.openSymbolFromReferences?%5B%22tail%20-f%20%2Fpath%2Fto%2Fyour%2Fproject%2Fstorage%2Flogs%2Flaravel.log%22%2C%5B%7B%22uri%22%3A%7B%22%24mid%22%3A1%2C%22fsPath%22%3A%22c%3A%5C%5Cbisoet%5C%5CBiosecurityTech%5C%5CREADME.md%22%2C%22_sep%22%3A1%2C%22external%22%3A%22file%3A%2F%2F%2Fc%253A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22path%22%3A%22%2FC%3A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22scheme%22%3A%22file%22%7D%2C%22pos%22%3A%7B%22line%22%3A251%2C%22character%22%3A41%7D%7D%5D%5D "Go to definition")
   - Test the application in a web browser

Remember to replace `/path/to/your/project` with the actual path to your project and [`yourdomain.com`](command:_github.copilot.openSymbolFromReferences?%5B%22yourdomain.com%22%2C%5B%7B%22uri%22%3A%7B%22%24mid%22%3A1%2C%22fsPath%22%3A%22c%3A%5C%5Cbisoet%5C%5CBiosecurityTech%5C%5CREADME.md%22%2C%22_sep%22%3A1%2C%22external%22%3A%22file%3A%2F%2F%2Fc%253A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22path%22%3A%22%2FC%3A%2Fbisoet%2FBiosecurityTech%2FREADME.md%22%2C%22scheme%22%3A%22file%22%7D%2C%22pos%22%3A%7B%22line%22%3A186%2C%22character%22%3A25%7D%7D%5D%5D "Go to definition") with your actual domain or server IP address where appropriate.
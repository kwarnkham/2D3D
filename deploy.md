sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1-fpm
sudo apt install php8.1-bcmath
sudo apt install php8.1-curl
sudo apt install php8.1-dom
sudo apt install php8.1-mbstring
sudo apt install php8.1-mysql
sudo apt install nginx
curl -4 icanhazip.com

sudo mkdir -p /var/www/2D3D && cd /var/www/2D3D
git clone https://github.com/kwarnkham/2D3D.git
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
apt install composer
composer install --optimize-autoloader --no-dev

sudo chown -R www-data:www-data /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache
sudo chmod -R 755 /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache

<!-- sudo chgrp -R www-data /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache
sudo chmod -R ug+rwx /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache -->

php artisan optimize:clear
php artisan route:cache
php artisan view:cache

supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:\*
supervisorctl status
php artisan queue:restart

sudo nano /etc/nginx/sites-available/2d3d.madewithheart.tech
sudo ln -s /etc/nginx/sites-available/2d3d.madewithheart.tech /etc/nginx/sites-enabled/
sudo nano /etc/nginx/nginx.conf
sudo nginx -t
sudo systemctl restart nginx

sudo ufw allow 'Nginx HTTP'
sudo ufw allow 'OpenSSH'
sudo ufw enable

sudo apt install certbot python3-certbot-nginx
sudo ufw allow 'Nginx HTTPS'
sudo certbot --nginx

sudo systemctl status certbot.timer
sudo certbot renew --dry-run

sudo mkdir -p /var/www/online_T && cd /var/www/online_T

```
//upload the apk
scp spa.zip root@2d3d.madewithheart.tech:/root/
rm -r backup/* && mv ./* backup
mv /root/spa.zip ./spa.zip && unzip spa.zip && rm spa.zip && mv spa/* ./ && rm -r spa
php artisan down
git pull
php artisan tinker
AppVersion::create(['url'=>env('AWS_URL') . '/Apk/LuckyHi/LuckyHi.apk', 'version'=>'1.0.11'])


php artisan optimize:clear
php artisan route:cache
php artisan view:cache

supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:\*
supervisorctl status
php artisan queue:restart

sudo chown -R www-data:www-data /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache
sudo chmod -R 755 /var/www/2D3D/storage /var/www/2D3D/bootstrap/cache

systemctl restart nginx
php artisan up

```

sudo chown -R www-data:www-data /var/www/online_T
sudo chmod -R 755 /var/www/online_T
nano /etc/nginx/sites-available/lucky-hi.madewithheart.tech
sudo ln -s /etc/nginx/sites-available/lucky-hi.madewithheart.tech /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo certbot --nginx

sudo ufw delete allow 'Nginx HTTP'

sudo apt install mysql-server
sudo systemctl status mysql.service
sudo mysql*secure_installation
CREATE USER 'moon'@'localhost' IDENTIFIED BY 'ninja@A1';
GRANT PRIVILEGE ON database.table TO 'username'@'host';
GRANT ALL PRIVILEGES ON *.\_ TO 'moon'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
php artisan migrate --seed
php artisan set:bot

git config credential.helper store

domain change notice
laravel .env
cors of spaces and cdn
app version and payment from db
modify crontab
modify queue supervis conf

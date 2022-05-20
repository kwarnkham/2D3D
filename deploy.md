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

sudo mkdir -p /var/www/2d3d.itismoon.fun && cd /var/www/2d3d.itismoon.fun
git clone https://github.com/kwarnkham/2D3D.git
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
apt install composer
composer install --optimize-autoloader --no-dev
sudo chown -R www-data:www-data /var/www/2d3d.itismoon.fun
sudo chmod -R 755 /var/www/2d3d.itismoon.fun
sudo chmod -R 755 /var/www

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

sudo nano /etc/nginx/sites-available/2d3d.itismoon.fun
sudo ln -s /etc/nginx/sites-available/2d3d.itismoon.fun /etc/nginx/sites-enabled/
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

sudo mkdir -p /var/www/lucky-hi.itismoon.fun && cd /var/www/lucky-hi.itismoon.fun

scp ~/Desktop/spa.zip root@2d3d.itismoon.fun:/root/
scp C:\Users\kwarn\Projects\online_T\dist\spa.zip root@2d3d.itismoon.fun:/root/

```
rm -r backup/* && mv ./* backup
mv /root/spa.zip ./spa.zip && unzip spa.zip && rm spa.zip && mv spa/* ./ && rm -r spa
systemctl restart nginx
```

sudo chown -R www-data:www-data /var/www/lucky-hi.itismoon.fun
sudo chmod -R 755 /var/www/lucky-hi.itismoon.fun
nano /etc/nginx/sites-available/lucky-hi.itismoon.fun
sudo ln -s /etc/nginx/sites-available/lucky-hi.itismoon.fun /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo certbot --nginx -d lucky-hi.itismoon.fun

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

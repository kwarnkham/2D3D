server {
listen 80;
listen [::]:80;
server_name 2d3d.madewithheart.tech;
root /var/www/2D3D/public;

add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";

index index.php;

charset utf-8;

location / {
try_files $uri $uri/ /index.php?$query_string;
}

location = /favicon.ico { access_log off; log_not_found off; }
location = /robots.txt { access_log off; log_not_found off; }

error_page 404 /index.php;

location ~ \.php$ {
fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
include fastcgi_params;
}

location ~ /\.(?!well-known).* {
deny all;
}
}

server {
listen 80;
server_name lucky-hi.madewithheart.tech;

root /var/www/online_T;

add_header X-Frame-Options "SAMEORIGIN";
add_header X-XSS-Protection "1; mode=block";
add_header X-Content-Type-Options "nosniff";

index index.html;

charset utf-8;

location / {
try_files $uri $uri/ /index.html;
}

location = /robots.txt { access_log off; log_not_found off; }

access_log off;
error_log /var/log/nginx/lucky-hi.madewithheart.tech-error.log error;

location ~ /\.(?!well-known).* {
deny all;
}
}
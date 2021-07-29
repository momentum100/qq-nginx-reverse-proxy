# qq-nginx-reverse-proxy

SERVER SETUP (UBUNTU)

atp-get update

apt-get -y install nginx php 

apt-get -y install certbot python3-certbot-nginx 

unlink /etc/nginx/sites-enabled/default

git clone https://github.com/momentum100/qq-nginx-reverse-proxy


SSH TO SERVER
#cd qq-nginx-reverse-proxy

Edit domainlist.txt (remove dummy word) file with vim or upload it via ssh

php start.php DESTINATIONIP

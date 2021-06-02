<?php
if (strlen($argv[1]==0)) { die ("destination ip NOT set php start.php 1.2.3.4\n"); }

// create directories from domainlist.txt
$domains = file ("domainlist.txt");
$serverIP = file_get_contents("http://169.254.169.254/metadata/v1/interfaces/public/0/ipv4/address");
$nginx_path = "/etc/nginx/sites-enabled/reverse-proxy.conf";

$vhost_header = file_get_contents("vhost-header.txt");
$vhost = str_replace ("DESTINATIONIP", $argv[1], $vhost_header);
$goodCount=0;


for ($i=0; $i< count($domains); $i++) {
	$d = trim($domains[$i]);
	
	// check if domains are pointed to server IP
	//
	echo "$d $i\n";
	
	if (gethostbyname($d) == $serverIP) {
		
		mkdir ("/var/www/". $d);	
		// generate certificate
		//
		$cmd = "certbot certonly -n --agree-tos --no-redirect --nginx --register-unsafely-without-email -d $d -w /var/www/$d\n" ;
		`$cmd`;
		
		// add domain config to $vhost
		//
		$tmpl = file_get_contents("vhost-template.txt");
		$tmpl = str_replace ("DOMAIN", $d, $tmpl);
		$tmpl = str_replace ("DESTINATIONIP", $argv[1], $tmpl);
		
		$vhost .= "\n\n". $tmpl;
		$goodCount++;

	}
	else {
		echo "Domain is not pointed to $serverIP\n";
	}
}


if ($goodCount>0) {
	// save nginx config
	file_put_contents($nginx_path, $vhost);

	// test & restart nginx
	`nginx -t`;
	`systemctl restart nginx`;
	
}

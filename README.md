# Gemini-PHP
Gemini-PHP is a Gemini server written in PHP by @neil@glasgow.social.
It's designed more for teaching than practical use.  That's said - it's very simple to get up and running and we're hosting this page on it - it seems to be performing well.
If you have any questions or want to get in touch, you can join our community on Matrix at #gemini-php:glasgow.social

## How to install
* Download via git
```
git clone https://coding.openguide.co.uk/git/gemini-php/
```

* Enter the project directory and create a certificate for your server (a self signed certificate is fine, in fact it's encouraged!)
```
cd gemini-php
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365
```
* Combine your private key with the certificate and put in the certs directory

```
cp cert.pem certs/combined.pem
cat key.pem >> certs/combined.pem
```
* Create a config file from the sample
```
cp config.php.sample config.php
```
* Then edit it with the location of your new certificate - most other options are optional
* Start your server with
```
php server.php
```
* You should be able to visit your new server in any Gemini client (remember to open your firewall if needed - post 1965)

## Using Gemini-PHP
* The basic index file is located in hosts/default/index.gemini - edit this to get started
* Gemini-PHP supports multiple virtual hosts, just create a directory with the name of the domain you expect to receive requests for, i.e.
```
mkdir hosts/glasgow.social
mkdir hosts/projects.glasgow.social
```

## Running as a service
To set up the server as a service, create the following file in /etc/systemd/system/gemini-php.service
```
[Unit]
Description=Gemini-PHP Service

[Service]
User=gemini
Type=simple
TimeoutSec=0
WorkingDirectory=/home/gemini/gemini-php/
PIDFile=/var/run/gemini-php.pid
ExecStart=/usr/bin/php -f /home/gemini/gemini-php/server.php
KillMode=process

Restart=on-failure
RestartSec=42s

[Install]
WantedBy=default.target
```
Note, customise the above to the user you are running gemini-php as (we recommend creating a new user account for this to keep it relatively isolated) as well as the path to the script.
Enable the script with systemctl
```
sudo systemctl enable gemini-php
sudo systemctl start gemini-php
systemctl status gemini-php

sudo systemctl stop gemini-php
```

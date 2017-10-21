#!/bin/bash

#To update the repository
sudo apt-get update -y

#install python-pip
sudo apt-get install -y python-setuptools python-pip

#install aws CLI, this will be used to accesss the aws element from the instance
sudo pip install awscli

#install apache2, php, php-mysql, curl, php-curl, php-xm, zip, unzip, git

sudo apt-get install -y git apache2 php curl php-curl php-mysql php-xml php7.0-xml zip unzip libapache2-mod-php php-gd

#cd ~

#curl -sS https://getcomposer.org/installer | php

#php composer.phar require aws/aws-sdk-php

#To enable apache and start for just safety purpose
sudo systemctl enable apache2
sudo systemctl start apache2

#sudo cp -ar  ~/vendor /var/www/html

sudo service apache2 restart

cd /var/www/html

sudo wget https://www.dropbox.com/s/342xsyhg1zsdz71/switchonarex.png


#Clone the private repo into the instance
git clone git@github.com:illinoistech-itm/krose1.git

sudo cp /krose1/s3test.php /var/www/html

cd /var/www/html/krose1

sudo cp s3test.php /var/www/html/
sudo cp dbtest.php /var/www/html/
sudo cp index.php /var/www/html/
sudo cp welcome.php /var/www/html/
sudo cp upload.php /var/www/html/
sudo cp uploader.php /var/www/html/
sudo cp gallery.php /var/www/html/
sudo cp image_validation.php /var/www/html/
sudo cp checkuploadenabled.php /var/www/html/
sudo cp admin.php /var/www/html/
sudo cp backup.php /var/www/html/
sudo cp logout.php /var/www/html/
sudo cp changestatus.php /var/www/html/
sudo cp restore.php /var/www/html/

cd ~

export COMPOSER_HOME=/root && /usr/bin/composer.phar self-update 1.0.0-alpha11

curl -sS https://getcomposer.org/installer | php

export COMPOSER_HOME=/root && /usr/bin/composer.phar self-update 1.0.0-alpha11

php composer.phar require aws/aws-sdk-php


echo "--------------------------"
echo "new try"
echo "--------------------------"

#php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
#php composer-setup.php
#php -r "unlink('composer-setup.php');"

#composer.phar require aws/aws-sdk-php

sudo cp -ar  ~/vendor /var/www/html

sudo cp -ar /root/vendor /var/www/html

sudo setfacl -m u:www-data:rwx /home/ubuntu

echo 'www-data  ALL=(ALL:ALL) ALL' >> /etc/sudoers

echo 'apache  ALL=(ALL:ALL) ALL' >> /etc/sudoers

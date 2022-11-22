# Ubuntu 22.04

1. Add a user
sudo useradd user1
sudo usermod -aG sudo user1

2. Downgrade OpenSSL version from 3.0 to 1.1.1
cd ~
wget https://www.openssl.org/source/openssl-1.1.1q.tar.gz
tar -zxf openssl-1.1.1q.tar.gz && cd openssl-1.1.1q
./config
sudo apt update
sudo apt install make gcc
sudo make
sudo make test
sudo mv /usr/bin/openssl ~/tmp
sudo make install
sudo ln -s /usr/local/bin/openssl /usr/bin/openssl
sudo ldconfig
openssl version

3. Install PHP 8.* (ZTS) + parallel
cd ~
sudo apt-get update
sudo apt-get install build-essential pkg-config autoconf bison re2c libxml2-dev \
libssl-dev libsqlite3-dev libcurl4-openssl-dev libpng-dev libjpeg-dev \
libonig-dev libfreetype6-dev libzip-dev libtidy-dev libwebp-dev libltdl7 libltdl-dev git unzip

sudo apt-get purge php8.*

VERSION=8.0.22
wget -qO- https://www.php.net/distributions/php-${VERSION}.tar.gz | tar -xz
cd php-${VERSION}/ext
git clone --depth=1 https://github.com/krakjoe/parallel.git

cd ..
./buildconf --force -shared

./configure \
    --prefix=/etc/php8z \
    --with-config-file-path=/etc/php8z \
    --with-config-file-scan-dir=/etc/php8z/conf.d \
    --disable-cgi \
    --with-zlib \
    --with-zip \
    --with-openssl \
    --with-curl \
    --enable-mysqlnd \
    --with-mysqli=mysqlnd \
    --with-pdo-mysql=mysqlnd \
    --enable-pcntl \
    --enable-gd \
    --enable-exif \
    --with-jpeg \
    --with-freetype \
    --with-webp \
    --enable-bcmath \
    --enable-mbstring \
    --enable-calendar \
    --with-tidy \
    --enable-zts \
    --enable-parallel \
    --enable-sockets

sudo make -j$(nproc)
sudo make install

sudo cp php.ini-development /etc/php8z/php.ini
sudo ln -s /etc/php8z/bin/php /usr/bin/php

sudo mkdir /etc/php8z/conf.d
sudo nano /etc/php8z/conf.d/additional.ini
////////////// additional.ini //////////////
memory_limit=-1

[opcache]
zend_extension=opcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=512
opcache.interned_strings_buffer=128
////////////////////////////////////////////

4. Usage phpz + parallel
cd ~
nano parallel.php
php parallel.php

5. Install Composer
cd ~
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
HASH=`curl -sS https://composer.github.io/installer.sig`
echo $HASH
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer

6. Symfony/Panther
cd ~
sudo apt update
sudo apt install -y python3-pip
pip3 install selenium webdriver-manager
sudo apt -y install firefox
sudo apt -y install google-chrome-stable

mkdir scrap
cd scrap
composer init
composer req symfony/panther
composer require --dev dbrekelmans/bdi
mkdir drivers
cd drivers
wget https://chromedriver.storage.googleapis.com/104.0.5112.79/chromedriver_linux64.zip
unzip chromedriver_linux64.zip
wget https://github.com/mozilla/geckodriver/releases/download/v0.31.0/geckodriver-v0.31.0-linux64.tar.gz
tar -xvf geckodriver-v0.31.0-linux64.tar.gz
sudo cp geckodriver /usr/local/bin/

cd ..
nano panther.php
php panther.php

7. chrome-php/chrome
composer require chrome-php/chrome
nano chrome-php.php
php chrome-php.php

8. behat/mink
composer require --dev behat/mink
composer require --dev behat/mink-goutte-driver
nano mink.php
php mink.php

9. behat/mink-selenium2-driver
sudo apt-get remove firefox
cd /opt
sudo wget http://ftp.mozilla.org/pub/firefox/releases/101.0.1/linux-x86_64/en-US/firefox-101.0.1.tar.bz2
sudo bunzip2 firefox-101.0.1.tar.bz2
sudo tar xvf firefox-101.0.1.tar
sudo ln -s /opt/firefox/firefox /usr/bin/firefox
sudo apt-get install libdbus-glib-1-2 xvfb
Xvfb :10 -screen 0 1600x1200x16

cd ~/scrap
sudo apt update
sudo apt install default-jre
java -version
curl -L https://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.59.jar > selenium-server-standalone-3.141.59.jar
export DISPLAY=:10
java -jar selenium-server-standalone-3.141.59.jar

composer require behat/mink-selenium2-driver
nano mink2.php
php mink2.php

# CentOS 7.9

1. Add a user
sudo adduser user1
sudo passwd user1
sudo usermod -a -G wheel user1

2. Downgrade OpenSSL version from 3.0 to 1.1.1
cd ~
sudo yum -y update
sudo yum install -y make gcc perl-core pcre-devel wget zlib-devel
wget https://ftp.openssl.org/source/openssl-1.1.1q.tar.gz
tar -xzvf openssl-1.1.1q.tar.gz
cd openssl-1.1.1q
./config --prefix=/usr --openssldir=/etc/ssl --libdir=lib no-shared zlib-dynamic
sudo make
sudo make test
sudo make install
sudo ldconfig
openssl version

3. Install PHP 8.* (ZTS) + parallel
cd ~
sudo yum -y update
sudo yum -y install epel-release autoconf bison libxml2-devel \
openssl-devel sqlite-devel libcurl-devel libpng-devel libjpeg-devel \
freetype-devel libwebp-devel git unzip nano bzip2
sudo yum -y libtidy-devel
sudo yum -y install oniguruma-devel -- enablerepo=epel
wget https://rpms.remirepo.net/enterprise/7/remi/x86_64/libzip-last-1.1.3-1.el7.remi.x86_64.rpm
sudo yum -y install libzip-last-1.1.3-1.el7.remi.x86_64.rpm
wget https://rpms.remirepo.net/enterprise/7/remi/x86_64/libzip-last-devel-1.1.3-1.el7.remi.x86_64.rpm
sudo yum -y install libzip-last-devel-1.1.3-1.el7.remi.x86_64.rpm

sudo yum remove php*

VERSION=8.0.22
wget -qO- https://www.php.net/distributions/php-${VERSION}.tar.gz | tar -xz
cd php-${VERSION}/ext
git clone --depth=1 https://github.com/krakjoe/parallel.git

cd ..
./buildconf --force -shared

./configure \
    --prefix=/etc/php8z \
    --with-config-file-path=/etc/php8z \
    --with-config-file-scan-dir=/etc/php8z/conf.d \
    --disable-cgi \
    --with-zlib \
    --with-zip \
    --with-openssl \
    --with-curl \
    --enable-mysqlnd \
    --with-mysqli=mysqlnd \
    --with-pdo-mysql=mysqlnd \
    --enable-pcntl \
    --enable-gd \
    --enable-exif \
    --with-jpeg \
    --with-freetype \
    --with-webp \
    --enable-bcmath \
    --enable-mbstring \
    --enable-calendar \
    --with-tidy \
    --enable-zts \
    --enable-parallel \
    --enable-sockets

sudo make -j$(nproc)
sudo make install

sudo cp php.ini-development /etc/php8z/php.ini
sudo ln -s /etc/php8z/bin/php /usr/bin/php

sudo mkdir /etc/php8z/conf.d
sudo nano /etc/php8z/conf.d/additional.ini
////////////// additional.ini //////////////
memory_limit=-1

[opcache]
zend_extension=opcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=512
opcache.interned_strings_buffer=128
////////////////////////////////////////////

4. Usage phpz + parallel
cd ~
nano parallel.php
php parallel.php

5. Install Composer
cd ~
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
HASH=`curl -sS https://composer.github.io/installer.sig`
echo $HASH
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer

6. Symfony/Panther
cd ~
sudo yum update -y
sudo yum install -y python3-pip
sudo pip3 install selenium webdriver-manager
sudo yum -y install firefox
wget https://dl.google.com/linux/direct/google-chrome-stable_current_x86_64.rpm
sudo yum localinstall google-chrome-stable_current_x86_64.rpm

mkdir scrap
cd scrap
composer init
composer req symfony/panther
composer require --dev dbrekelmans/bdi
mkdir drivers
cd drivers
wget https://chromedriver.storage.googleapis.com/104.0.5112.79/chromedriver_linux64.zip
unzip chromedriver_linux64.zip
wget https://github.com/mozilla/geckodriver/releases/download/v0.31.0/geckodriver-v0.31.0-linux64.tar.gz
tar -xvf geckodriver-v0.31.0-linux64.tar.gz
sudo cp geckodriver /usr/local/bin/

cd ..
nano panther.php
php panther.php

7. chrome-php/chrome
composer require chrome-php/chrome
nano chrome-php.php
php chrome-php.php

8. behat/mink
composer require --dev behat/mink
composer require --dev behat/mink-goutte-driver
nano mink.php
php mink.php

9. behat/mink-selenium2-driver
sudo yum remove firefox
cd /opt
sudo wget http://ftp.mozilla.org/pub/firefox/releases/57.0.4/linux-x86_64/en-US/firefox-57.0.4.tar.bz2
sudo bunzip2 firefox-57.0.4.tar.bz2
sudo tar xvf firefox-57.0.4.tar
sudo ln -s /opt/firefox/firefox /usr/bin/firefox
sudo yum -y install dbus-glib Xvfb
Xvfb :10 -screen 0 1600x1200x16

cd ~/scrap
sudo yum -y update
sudo yum -y install java
java -version
curl -L https://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar > selenium-server-standalone-3.8.1.jar
export DISPLAY=:10
java -jar selenium-server-standalone-3.8.1.jar

composer require behat/mink-selenium2-driver
nano mink2.php
php mink2.php

# Server installtion (172.104.128.102)
- Apache2
  - sudo apt install apache2
- MySql
  - sudo apt install mysql-server
- PHP
  - sudo apt install php libapache2-mod-php php-mysql
      - (sudo systemctl restart apache2) 
- DCMTK
  - sudo apt-get install dcmtk
- phpMyAdmin
  - sudo apt-get install phpmyadmin php-mbstring php-gettext
  - CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost';
FLUSH PRIVILEGES;

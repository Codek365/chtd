#!/bin/bash
echo "Install...."  

php upload/install/cli_install.php install --db_hostname localhost \
                              --db_username root \
                              --db_password root \
                              --db_database cuahangtrieudo \
                              --db_driver mysqli \
                              --db_port 3306 \
                              --username admin \
                              --password admin \
                              --email youremail@example.com \
                              --http_server http://cuahangtrieudo.com/
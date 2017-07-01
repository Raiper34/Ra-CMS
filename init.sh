#!/bin/bash

bower install
composer install

sudo mkdir log
sudo mkdir temp
sudo mkdir temp/cache
sudo mkdir temp/sessions

sudo chmod 777 log
sudo chmod 777 temp
sudo chmod 777 temp/cache
sudo chmod 777 temp/sessions
sudo chmod 777 js
sudo chmod 777 css
sudo chmod 777 upload






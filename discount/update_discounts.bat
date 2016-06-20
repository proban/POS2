@echo off

START /B /MIN C:\xampp\php\php.exe C:\xampp\htdocs\Dropbox\discount\update_discounts_cabang.php
START /B /MIN C:\xampp\php\php.exe C:\xampp\htdocs\Dropbox\discount\update_discount_locations.php
START /B /MIN C:\xampp\php\php.exe C:\xampp\htdocs\Dropbox\discount\update_discount_products.php

exit
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Set env
install XAMPP and Composer

## install laravel installer
composer global require laravel/installer

## Create the project files 
laravel new Dash_Task

## run the apache and mysql in Xampp
apache for the server
mysql for the DB

## run the project 
php artisan serve

## Step 2

php artisan make:model Category -mf
php artisan make:model Product -mf
php artisan make:model Customer -mf
php artisan make:model Order -mf
php artisan make:model OrderItem -mf
php artisan make:migration create_reports_table

## Step 3 

Admin Email :'admin@dash.test'
Admin Password : 'password'
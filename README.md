# Petshop BackEnd

It is an API  for Ecommerce frontend where the Backend work to handle the data to server has been taken care of.

## Table of Contents

- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Testing](#testing)
- [Additional Details](#additional-details)

## Getting Started

Please use following instruction to take care to install the project on your server or local machine


### Prerequisites

List any software and dependencies that need to be installed to run this project. Include instructions on how to install them.

- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/) (or any other supported database)

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/bharatv03/petshop-be.git

2. Install libraries 

    composer install

## Usage

After cloning is done please use following commands to activate your project

1. This command will help you point the project to accurate Database

    cp .env.example .env

2. This command will activate your above file on the project for being able to use
    
    php artisan config:cache

3. The below command will create database without letting you create it manually
    
    php artisan make:database petshop

4. The below command will create all the tables that are being used for this project
    
    php artisan migrate

5. The below command will help you create the admin user entry 

    php artisan db:seed

6. The below command will let your login procedure work smoothly

    php artisan jwt:secret

7. The command below will start the server on local machine

    php artisan serve

8. After running the server command your URL will be active with below URL:

    https://localhost:8000

The above commands will help you create project run successfully to utilize all the features built

## Features

1. Admin Module
    - Admin account creation
    - Admin Login
    - User List
    - User Delete not Admin
    - Edit User
    - Logout
2. User Module
    - User Registration
    - User Login
    - User View
    - User Edit
    - User Delete
    - Logout
    - Forgot Password
    - Reset Password

## Testing

    For Testing purpose you use command  in ``Bash this will help you to test all the routes have been created

    php artisan test

## Additional Details

    The API document is available on where we can find all the URLs:
    https://localhost:8000/api/documentation#/

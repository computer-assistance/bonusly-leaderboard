## Synopsis

#### This is a Laravel 5.4 project

Bonusly leaderboard is an open source Laravel (5.4) project developed in-house at Computer Assistance, Oxford, UK after we signed up for Bonusly (https://bonus.ly/ - peer to peer team-member recognition and rewards)

We realised early on that the bonus notifications provided by Bonusly were not going to suit our needs so rolled this project out in the space of a week or so.

## Example
<img src="https://github.com/computer-assistance/bonusly-leaderboard/blob/master/bonusly-thumb-300x190.png" style="text-align:center;">

The app makes 2 calls to the Bonusly api:
 1. Retrieve all user data
 2. Retrieve monthly bonus data

This data is then analysed by the application, summed and sorted and then pushed out to the view generator.

## Motivation

We felt that this was a much-needed application as far as our organization was concerned and feel this adds an at-a-glance, real-time feature, thereby adding value to our Bonusly usage thus enhancing their project's worth to us.

## Installation

#### Preliminaries

 1. An active subscription to Bonusly for your organisation/team
 2. An API key is required and is easily setup using their API dashboard at https://bonusly.gelato.io/ (you must sign in to your developer account)
 3. PHP 5.6 or above
 4. Composer. Composer is a dependency manager for PHP. You can read more about download composer from their official website at this link https://getcomposer.org/. We will not cover how to install composer here
 5. A Mysql database

#### Installing project

 1. Clone this repo into the root of your projects or www/html folder - it will add its own directory /bonusly-leaderboard
   eg. In terminal or at the command prompt, if in /projects or /www/html or any other folder that's configured as your server's root folder type in:
     'git clone' and then paste in the url which can be copied to your clipboard easily by clicking the green 'Clone or download' button at the top right of this page.
     [see here for more help with cloning a git project](https://help.github.com/articles/cloning-a-repository/)
 2. Cd into the created folder and run 'composer install'
 4. Copy .env.example to .env (Linux/Mac: cp .env.example .env | Windows cmd prompt: copy .env.example .env)
    The vast majority of the .env file can be left as is as most of it is unused in this project
 5. Create a new Laravel application key by using the php artisan generate command 'php artisan key:generate' - it is automatically added to the .env file
 6. Add your bonusly API key (mentioned in Preliminaries 2. above) to this line in the .env file | BONUSLY_TOKEN=null
      (Replace null with your Bonusly API key. There should NOT be any spaces between BONUSLY_TOKEN= and the key i.e. BONUSLY_TOKEN=your_key_here)
 7. a. A Mysql database
      1. Log into Mysql
      2. At the mysql command prompt use the create database command - 'mysql> create database bonusly' (or any name you like but you have to use that name in step 7.b )
 7. b. Configuring Laravel to use this database
      1. Open you .env file in your project root folder
      2. Change the values below according to your setup

      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=your_database_name (the name you chose in the 7.a.2)
      DB_USERNAME=your_database_username
      DB_PASSWORD=your_database_password

  8. Run the php artisan migrate command to create the tables in the database
  9. Restart MAMP/WAMP/LAMP or apache for your new environment variables to be loaded and take effect
  10. Save your company logo image as a .png or .gif file in the public/img folder
      Image guidelines:
        1. Use an image format that supports a transparent background (not jpeg or your logo will have a box around it)
        2. Make your Logo 310 x 90 pixels or close to that.
  11. Modify .env file key BONUSLY_LOGO_IMG_FILE=null | replace null with your filename including extension


Once you have done all of that then you can either use php artisan serve and go to 127.0.0.1:8000 or localhost:8000 (php artisan default port) to see the project or if you're using Linux/Apache then follow this link for instructions on how to add a new virtualhost (how to add a new site!)
https://httpd.apache.org/docs/2.4/vhosts/examples.html (again restart apache afterwards)

## Special Features

#### Exclude undesirable users
Your organisation may have users that are not eligible for bonuses or not part of the scheme yet still have a bonusly account/profile such as managers or administrators. To exclude them from the results of any api calls the feature below was added.

In the .env file there is a key named BONUSLY_UNWANTED that can be used to exclude users by simply adding their username to it.
For multiple users you have to supply all the usernames in ['kebab' case](http://wiki.c2.com/?KebabCase) and they are picked up from there and automatically parsed. (You must not use any hyphenated usernames for obvious reasons)

A list of usernames for your organisation is easily obtained using the Bonusly API explorer using the settings below

![bonusly api screenshot](https://github.com/computer-assistance/bonusly-leaderboard/blob/master/public/img/bonusly_user_request_api.png)

## Troubleshooting

You may have to restart MAMP/WAMP/LAMP or apache again. If you have permissions issues then make sure files and folders have the necessary permissions. One folder that can catch you out quite often is the /storage folder and its subfolders and files


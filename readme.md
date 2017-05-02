## Synopsis

Bonusly leaderboard is an open source Laravel (5.4) project developed in-house at Computer Assistance, Oxford, UK after we signed up for Bonusly (https://bonus.ly/ - peer to peer team-member recognition and rewards).

We realised early on that the bonus notifications provided by Bonusly were not going to suit our needs so rolled this project out in the space of a week or so.

## Example
<img src="https://github.com/computer-assistance/bonusly-leaderboard/blob/master/bonusly-thumb-300x190.png" style="text-align:center;">

The app makes 2 calls to the Bonusly api
 1. Retrieve user data.
 2. Retrieve monthly bonus data.

This data is then analysed by the application, summed and sorted and pushed out to view genrator.

## Motivation

We felt that this was a much-needed application as far as our organization was concerned and feel this adds an at-a-glance, real-time feature, thereby adding value to our Bonusly usage thus enhancing their project's worth to us.

## Installation

#### Preliminaries

 1. Sign up for Bonusly for your organisation/team.
 2. An API key is required and is easily setup using their API dashboard at https://bonusly.gelato.io/ (you must sign in to your developer account.)
 3. Laravel 5.4
 4. PHP 5.6 or above
 5. Composer
      Composer is a dependency manager for PHP. You can read more about composer from their official website. You can download composer from this link. We will not cover how to install composer here.

#### Installing project

 1. Clone this repo into your projects folder (/sitess or /www/html or /http folder or whatever you use - I use /projects)
 2. Cd into the created folder (should be 'bonusly-leaderboard') and run 'composer install'.
 4. Copy .env.example to .env (Linux/Mac: cp .env.example .env | Windows cmd prompt: copy .env.example .env)
    The vast majority of the .env file can be left as is as most of it is unused in this project.
 5. Create a new Laravel application key by using the php artisan generate 'php artisan key:generate' - it is automatically added to the .env file
 6. Add your bonusly API key (mentioned in Preliminaries 2. above) to this line in the .env file | BONUSLY_TOKEN=null
      (Replace null with your Bonusly API key - no spaces between BONUSLY_TOKEN= and the key!)

Once you have done that then you can either use pho artisan serve and go to 127.0.0.1:8000 or localhost:8000 (php artisan default port) to see the project or if you're using Linux/Apache then follow this link for instructions on how to add a new virtualhost (which means how to add a new website really!)
https://httpd.apache.org/docs/2.4/vhosts/examples.html

If you are using MAMP/WAMP/LAMP then you should just browse to your project through the WAMP/MAMP/LAMP sites page.


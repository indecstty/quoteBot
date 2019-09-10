## Quote Bot
Quote Bot for [Telegram](https://telegram.org)
### Requirements
* Publicly running server with SSL certificate
* PHP 5.x
* [Composer](https://getcomposer.org)
* [Telegram Bot created and set up](https://core.telegram.org/bots) (webhook for the bot will be set up later)
* A dedicated database and respective user created
### Installation on Debian based servers
On the server on the folder you want to install the bot (do not install on a folder available publicly via http!):
1. `git clone https://github.com/makinenmo/quoteBot.git`
2. `cd quoteBot`
3. `composer install`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. Modify `.env`:
6.1. `DB_*`: modify to match the properties of the database and db user you created earlier
6.2. `BOT_TOKEN`: Token of the bot you created earlier
7. `php artisan migrate`
8. Make the `public/` folder available over http by symlinking the folder to the http root of the server. For example: `ln -s /path/to/public/folder /var/www/html/bots/quoteBot`
9. Make sure that the installation was successful by navigating to bot's root url, for example: `https://[domain name]/bots/quoteBot`. You should see Lumen's default welcome page.
10. Modify `.env`: `APP_ENV=production`, `APP_DEBUG=false` 
11. Set the bot's webhook with Telegram Bot API's [https://core.telegram.org/bots/api#setwebhook](setWebhook) method. Continuing with examples on steps 8 and 9, the url would be `https://[domain name]/bots/quoteBot/[bot token (the same that was set up in .env)]`
### Licensing
Licensed under the [MIT license](http://opensource.org/licenses/MIT)

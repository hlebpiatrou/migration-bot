# Telegram bot for VMD
Telegram bot for time tracking of availability at VMD

## Installation

1). Clone repository:

`git clone https://github.com/hlebpiatrou/migration-bot.git`

2). Go to the project directory:
 
 `cd ./migration-bot`

3). Run Composer install:

`composer install`

4). Open app.php file and modify following settings:

`const TELEGRAM_CHAT_ID = <YOUR_TELEGRAM CHAT_ID>`

`const TELEGRAM_BOT_API_KEY = <YOUR_TELEGRAM_BOT_API_KEY>`

References: 
* [How to create a Telegram bot](https://core.telegram.org/bots#3-how-do-i-create-a-bot)
* [How to get a Telegram chat ID](https://stackoverflow.com/questions/32423837/telegram-bot-how-to-get-a-group-chat-id)

5). Open your Crontab:

`crontab -e`

6). Add the following line (don't forget to specify your own path to migration bot directory):

`* * * * * php -f <your_path_to_migration_bot_directory>/app.php`

This bot will check availability every 1 minute! 

**Notice!** If there will be available dates, the bot won't stop itself and continue to send you this information every 1 minute.

### Have fun!
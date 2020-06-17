
Integrating OJS Email Notifications with Telegram BOT.

## Requirements
- OJS Version >= 3.0.0
- Installed php cURL extension
- Using HTTPS

## Configuration
#### Register a Telegram BOT
- Create a Telegram BOT and get an API Key. Please see [https://core.telegram.org/bots#3-how-do-i-create-a-bot](https://core.telegram.org/bots#3-how-do-i-create-a-bot)

#### Plugin Installing
- Download the latest version of this plugin [https://github.com/anggriyulio/telegramNotify/releases](https://github.com/anggriyulio/telegramNotify/releases). 
- Extract to generic plugin directory or install via plugin manager.
- Enable the plugin
- Set Telegram Bot Token in Plugin Setting section, also don forget to set the message for /start command and save it. 


#### Setup Webhook URL
- This plugin automatically creates a webhook URL for each hosted journal
- Register telegram bot webhook, so your OJS and Telegram can communicate in realtime. Just open the URL:

https://api.telegram.org/bot[BOT_TOKEN]/setWebhook?url=https://[DOMAIN]/index.php/[JOURNAL_PATH]/telegram-webhook/

Make sure to replace `[BOT_TOKEN]`, `[DOMAIN]` and `[JOURNAL_PATH]` and you got a response like this:
```json
{
  "ok": true,
  "result": true,
  "description": "Webhook was set"
}
```

## How it's work
All users who want to get notifications via Telegram must first open a conversation with the previously created BOT. And share contacts with bots to ensure that you are the owner of the account.

Feel free to send me an email or create an [issue](https://github.com/anggriyulio/telegramNotify/issues) if you have any problems ;)


## License
The telegramNotify plugin is open-sourced software licensed under the [GPL v3.0](http://www.gnu.org/licenses/gpl-3.0.html).


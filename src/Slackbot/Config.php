<?php

namespace Slackbot;

/**
 * Class Config.
 */
class Config extends AbstractConfig
{
    protected static $configs = [
        'baseUrl'              => 'http://localhost:8888',
        'timezone'             => 'Australia/Melbourne',
        'apiToken'             => 'YOUR_API_TOKEN',
        'channel'              => '#general',
        'botUserId'            => 'YOUR_BOT_USER_ID',
        'botUsername'          => 'YOUR_BOT_USERNAME',
        'chatLogging'          => true,
        'tmpFolderName'        => 'tmp',
        'chatLoggingFileName'  => 'chat_log',
        'iconURL'              => 'YOUR_BOT_ICON_URL_48_BY_48',
        'asUser'               => true,
        // possible values are: webhook, event
        'listenerType'         => 'webhook',
        // possible values are: slack, json, slashCommand
        'response'      => 'slack',
        // this is used if there is no command has been specified in the message
        'defaultCommand'     => 'help',
        'commandPrefix'      => '/',
        /*
         * Generic messages
         */
        'noCommandMessage' => "Sorry. I couldn't find any command in your message.
        List the available commands using /help",
        'unknownCommandMessage' => "Sorry. I do not know anything about your command: '/{command}'.
        List the available commands using /help",
        // leave it empty to disable it
        'confirmReceivedMessage' => ":point_right: {user}I've received your message and am thinking about that ...",
        /*
         * App credentials - This is required for Event listener
         * Can be found at https://api.slack.com/apps
         */
        'clientId'     => 'YOUR_APP_CLIENT_ID',
        'clientSecret' => 'YOUR_APP_SECRET',
        'scopes'       => ['bot'],
        /*
         * For interactive messages and events,
         * use this token to verify that requests are actually coming from Slack
         */
        'verificationToken' => 'YOUR_APP_VERIFICATION_TOKEN',
        'apiAppId'          => 'YOUR_API_ID',
        // This is mainly used for testing
        'respondOk'         => true,
    ];
}

<?php

namespace Slackbot\Tests;

use Slackbot\Command;
use Slackbot\Config;
use Slackbot\plugin\ping\Ping;
use Slackbot\Slackbot;

/**
 * Class SlackbotTest.
 */
class SlackbotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testSetGetRequest()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $slackbot = new Slackbot($request);

        $this->assertEquals($request, $slackbot->getRequest());

        $this->assertEquals($config->get('outgoingWebhookToken'), $slackbot->getRequest('token'));
    }

    /**
     * @throws \Exception
     */
    public function testGetConfig()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $slackbot = new Slackbot($request);

        $this->assertEquals($config, $slackbot->getConfig());
    }

    /**
     * @throws \Exception
     */
    public function testSetConfig()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $slackbot = new Slackbot($request);
        $slackbot->setConfig($config);

        $this->assertEquals($config, $slackbot->getConfig());
    }

    /**
     * @throws \Exception
     */
    public function testRespond()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text'  => $botUsername.' /ping',
        ];

        $slackbot = new Slackbot($request);
        $response = $slackbot->respond();

        $this->assertEquals('pong', $response);

        $message = '';

        $inputsOutputs = [
            [
                'i' => [
                    'message' => "$botUsername /ping",
                ],
                'o' => 'pong',
            ],
            [
                'i' => [
                    'message' => "$botUsername /pong",
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => '/ping',
                ],
                'o' => 'pong',
            ],
            [
                'i' => [
                    'message' => '/pong',
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => '/pong',
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => '/unknownCommand',
                ],
                'o' => $config->get('unknownCommandMessage', ['command' => 'unknownCommand']),
            ],
            [
                'i' => [
                    'message' => 'dummy message without any command',
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
            [
                'i' => [
                    'message' => 'sfdsf /ping',
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
            [
                'i' => [
                    'message' => 'ddfg dfdfg df gdfg',
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $response = $slackbot->respond($inputOutput['i']['message']);

            $output = $inputOutput['o'];

            if (is_callable($inputOutput['o'])) {
                $output = call_user_func($inputOutput['o'], $inputOutput['i']['message']);
            }

            $this->assertEquals($output, $response);
        }
    }

    /**
     * @param $message
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function outputOnNoCommand($message)
    {
        $config = new Config();
        $defaultCommand = $config->get('defaultCommand');

        $token = $config->get('outgoingWebhookToken');

        $slackbot = new Slackbot(['text' => $message, 'token' => $token]);

        if (!empty($defaultCommand)) {
            $command = (new Command())->get($defaultCommand);
            $commandClass = $command['class'];

            return (new $commandClass($slackbot))->index();
        }

        return $config->get('noCommandMessage');
    }

    /**
     * @throws \Exception
     */
    public function testRespondExceptException()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text'  => $botUsername.' /commandWithoutFunctionForTest',
        ];

        $this->setExpectedException(
            '\Exception',
            'Action / function: \'commandWithoutFunctionForTest\' does not exist in \'Slackbot\plugin\ping\Ping\''
        );

        $slackbot = new Slackbot($request);
        $slackbot->respond();
    }

    /**
     * @throws \Exception
     */
    public function testGetModuleAction()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $slackbot = new Slackbot($request);
        $result = $slackbot->getModuleAction('/ping message');

        $expected = [
            'module' => new Ping($slackbot),
            'action' => 'index',
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws \Exception
     */
    public function testGetModuleActionWithoutDefaultCommand()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $config->set('defaultCommand', '');

        $slackbot = new Slackbot($request);
        $result = $slackbot->getModuleAction('dummy message without command');

        $expected = [
            'error' => $config->get('noCommandMessage'),
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws \Exception
     */
    public function testSend()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
        ];

        $config->set('response', 'json');

        $slackbot = new Slackbot($request, $config);

        $this->expectOutputString('{"text":"test response"}');

        $slackbot->send('test response');
    }

    /**
     * @throws \Exception
     */
    public function testSendByBot()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token'   => $config->get('outgoingWebhookToken'),
            'user_id' => 'USLACKBOT',
        ];

        $config->set('response', 'json');

        try {
            new Slackbot($request, $config);
        } catch (\Exception $e) {
            $this->assertEquals('Request is not coming from Slack', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function testListenToSlack()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text'  => '/ping',
        ];

        $config->set('response', 'json');
        $config->set('chatLogging', false);

        $slackbot = new Slackbot($request, $config);

        $this->expectOutputString('{"text":"pong"}');

        $slackbot->listenToSlack();
    }
}

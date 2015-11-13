<?php

namespace Hexanet\Swiftmailer\Test;

use Hexanet\Swiftmailer\ImageEmbedPlugin;
use PHPUnit_Framework_TestCase;
use Swift_Mailer;
use Swift_Message;

class EmbedImagePluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function setUp()
    {
        $this->mailer = Swift_Mailer::newInstance(\Swift_NullTransport::newInstance());
        $this->mailer->registerPlugin(new ImageEmbedPlugin());
    }

    public function testHtmlBody()
    {
        $message = $this->createMessage();

        $html = <<<HTML
<html>
    <head></head>
    <body>
        <p>some text</p>
        <img src="fixtures/placeholder.png" alt="placeholder">
    </body>
</html>
HTML;

        $message->setBody($html, 'text/html');

        $this->mailer->send($message);

        $children = $message->getChildren();

        $this->assertInstanceOf('\Swift_Image', $children[0], 'Image is embedded in the message');
        $this->assertContains(
            sprintf('<img src="cid:%s" alt="placeholder">', $children[0]->getId()),
            $message->getBody(),
            'Image is linked in body'
        );
    }

    public function testHtmlPart()
    {
        $message = $this->createMessage();

        $html = <<<HTML
<html>
    <head></head>
    <body>
        <p>some text</p>
        <img src="fixtures/placeholder.png" alt="placeholder">
    </body>
</html>
HTML;

        $message->addPart($html, 'text/html');

        $this->mailer->send($message);

        $children = $message->getChildren();

        $this->assertInstanceOf('\Swift_Image', $children[1], 'Image is embedded in the message');
        $this->assertContains(
            sprintf('<img src="cid:%s" alt="placeholder">', $children[1]->getId()),
            $children[0]->getBody(),
            'Image is linked in body'
        );
    }

    /**
     * @return Swift_Message
     */
    private function createMessage()
    {
        $message = Swift_Message::newInstance();

        $message->setSubject('Test message');
        $message->setFrom('from@example.com');
        $message->setTo('to@example.com');

        return $message;
    }
}

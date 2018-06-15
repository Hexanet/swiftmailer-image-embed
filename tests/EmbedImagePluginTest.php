<?php

namespace Hexanet\Swiftmailer\Test;

use Hexanet\Swiftmailer\ImageEmbedPlugin;
use PHPUnit\Framework\TestCase;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;

class EmbedImagePluginTest extends TestCase
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function setUp()
    {
        $this->mailer = new Swift_Mailer(new Swift_NullTransport());
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
        <img src="%s" alt="placeholder">
    </body>
</html>
HTML;
        $html = sprintf($html, __DIR__ . '/fixtures/placeholder.png');

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
        <img src="%s" alt="placeholder">
    </body>
</html>
HTML;
        $html = sprintf($html, __DIR__ . '/fixtures/placeholder.png');

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

    public function testAttachment()
    {
        $message = $this->createMessage();
        $message->attach(Swift_Attachment::fromPath(__DIR__ . '/fixtures/placeholder.png'));

        $html = <<<HTML
<html>
    <head></head>
    <body>
        <p>some text</p>
        <img src="%s" alt="placeholder">
    </body>
</html>
HTML;
        $html = sprintf($html, __DIR__ . '/fixtures/placeholder.png');

        $message->setBody($html, 'text/html');

        $this->mailer->send($message);

        $children = $message->getChildren();

        $this->assertInstanceOf('\Swift_Attachment', $children[0], 'Image is embedded in the message');
        $this->assertInstanceOf('\Swift_Image', $children[1], 'Image is embedded in the message');
        $this->assertContains(
            sprintf('<img src="cid:%s" alt="placeholder">', $children[1]->getId()),
            $message->getBody(),
            'Image is linked in body'
        );
    }

    public function testBasePathAndUrl()
    {
        $this->mailer = new Swift_Mailer(new Swift_NullTransport());
        $this->mailer->registerPlugin(new ImageEmbedPlugin(__DIR__));

        $message = $this->createMessage();

        $html = <<<HTML
<html>
    <head></head>
    <body>
        <p>some text</p>
        <img src="%s" alt="localPath">
        <img src="%s" alt="url">
    </body>
</html>
HTML;
        $html = sprintf(
            $html,
            '/fixtures/placeholder.png',
            'https://github.com/Hexanet/swiftmailer-image-embed/raw/master/tests/fixtures/placeholder.png'
        );

        $message->setBody($html, 'text/html');

        $this->mailer->send($message);

        $children = $message->getChildren();

        $this->assertInstanceOf('\Swift_Image', $children[0], 'Local image is embedded in the message');
        $this->assertContains(
            sprintf('<img src="cid:%s" alt="localPath">', $children[0]->getId()),
            $message->getBody(),
            'Image is linked in body'
        );
        $this->assertInstanceOf('\Swift_Image', $children[1], 'Remote image is embedded in the message');
        $this->assertContains(
            sprintf('<img src="cid:%s" alt="url">', $children[1]->getId()),
            $message->getBody(),
            'Image is linked in body'
        );
    }

    /**
     * @return Swift_Message
     */
    private function createMessage()
    {
        $message = new Swift_Message();
        $message->setSubject('Test message');
        $message->setFrom('from@example.com');
        $message->setTo('to@example.com');

        return $message;
    }
}

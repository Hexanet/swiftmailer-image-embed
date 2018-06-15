<?php

namespace Hexanet\Swiftmailer;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use Swift_Image;
use Swift_Mime_SimpleMimeEntity;
use Swift_Mime_SimpleMessage;

class ImageEmbedPlugin implements Swift_Events_SendListener
{

    private $basePath = '';

    /**
     * ImageEmbedPlugin constructor.
     *
     * @param string $basePath
     */
    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
    }

    /**
     * @param Swift_Events_SendEvent $event
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        if ($message->getContentType() === 'text/html') {
            $message->setBody($this->embedImages($message));
        }

        foreach ($message->getChildren() as $part) {
            if (strpos($part->getContentType(), 'text/html') === 0) {
                $part->setBody($this->embedImages($message, $part), 'text/html');
            }
        }
    }

    /**
     * @param Swift_Events_SendEvent $event
     */
    public function sendPerformed(Swift_Events_SendEvent $event)
    {

    }

    /**
     * @param Swift_Mime_SimpleMessage         $message
     * @param Swift_Mime_SimpleMimeEntity|null $part
     *
     * @return string
     */
    protected function embedImages(Swift_Mime_SimpleMessage $message, Swift_Mime_SimpleMimeEntity $part = null)
    {
        $body = $part === null ? $message->getBody() : $part->getBody();

        $dom = new \DOMDocument('1.0');
        $dom->loadHTML($body);

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');

            /**
             * Prevent beforeSendPerformed called twice
             * see https://github.com/swiftmailer/swiftmailer/issues/139
             */
            if (strpos($src, 'cid:') === false) {

                $path = $src;

                if (filter_var($src, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === false) {
                    $path = $this->basePath . $src;
                }

                $entity = \Swift_Image::fromPath($path);
                $message->setChildren(
                    array_merge($message->getChildren(), [$entity])
                );

                $image->setAttribute('src', 'cid:' . $entity->getId());
            }
        }

        return $dom->saveHTML();
    }
}

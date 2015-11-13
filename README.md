# Swiftmailer Image Embed Plugin

[![Build Status](https://travis-ci.org/Hexanet/swiftmailer-image-embed.svg)](https://travis-ci.org/Hexanet/swiftmailer-image-embed)

Swiftmailer plugin to automatically embed images into message.

## Installation

```
composer require hexanet/swiftmailer-image-embed
```

## Usage

```php
use Hexanet\Swiftmailer\ImageEmbedPlugin;

$mailer = Swift_Mailer::newInstance();

$mailer->registerPlugin(new ImageEmbedPlugin());
```

## Licence

Swiftmailer Image Embed Plugin is released under the MIT License. See the [bundled LICENSE file](LICENSE) file for details.

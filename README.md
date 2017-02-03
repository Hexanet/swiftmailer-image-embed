# Swiftmailer Image Embed Plugin

[![Build Status](https://travis-ci.org/Hexanet/swiftmailer-image-embed.svg)](https://travis-ci.org/Hexanet/swiftmailer-image-embed) [![Total Downloads](https://poser.pugx.org/hexanet/swiftmailer-image-embed/downloads.png)](https://packagist.org/packages/hexanet/swiftmailer-image-embed) [![Latest Stable Version](https://poser.pugx.org/hexanet/swiftmailer-image-embed/v/stable.png)](https://packagist.org/packages/hexanet/swiftmailer-image-embed)

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

## Credits

Developed by the SI Team of [Hexanet](http://www.hexanet.fr/).

## License

[Swiftmailer Image Embed Plugin](https://github.com/Hexanet/swiftmailer-image-embed) is licensed under the [MIT license](LICENSE).

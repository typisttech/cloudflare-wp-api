# Cloudflare WP API

[![Latest Stable Version](https://poser.pugx.org/typisttech/cloudflare-wp-api/v/stable)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![Total Downloads](https://poser.pugx.org/typisttech/cloudflare-wp-api/downloads)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![Build Status](https://travis-ci.org/TypistTech/cloudflare-wp-api.svg?branch=master)](https://travis-ci.org/TypistTech/cloudflare-wp-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TypistTech/cloudflare-wp-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TypistTech/cloudflare-wp-api/?branch=master)
[![codecov](https://codecov.io/gh/TypistTech/cloudflare-wp-api/branch/master/graph/badge.svg)](https://codecov.io/gh/TypistTech/cloudflare-wp-api)
[![PHP Versions Tested](http://php-eye.com/badge/typisttech/cloudflare-wp-api/tested.svg)](https://travis-ci.org/TypistTech/cloudflare-wp-api)
[![Dependency Status](https://gemnasium.com/badges/github.com/TypistTech/cloudflare-wp-api.svg)](https://gemnasium.com/github.com/TypistTech/cloudflare-wp-api)
[![Latest Unstable Version](https://poser.pugx.org/typisttech/cloudflare-wp-api/v/unstable)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![License](https://poser.pugx.org/typisttech/cloudflare-wp-api/license)](https://packagist.org/packages/typisttech/cloudflare-wp-api)

WordPress HTTP API wrapper around the [jamesryanbell/cloudflare](https://packagist.org/packages/jamesryanbell/cloudflare) package.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [To be continued..](#to-be-continued)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->


## Install

Installation should be done via composer, details of how to install composer can be found at [https://getcomposer.org/](https://getcomposer.org/).

``` bash
$ composer require typisttech/cloudflare-wp-api
```

## Usage

Create a connection using ``Cloudflare\WP\Api`` first and then use it like the original package e.g.

``` php
use Cloudflare\Zone\Dns;
use Cloudflare\WP\Api;

// Create a Cloudflare\WP\Api client
$client = new Cloudflare\WP\Api('email@example.com', 'API_KEY');

// Use it like the original package
$dns = new Cloudflare\Zone\Dns($client);
$dns->create('12345678901234567890', 'A', 'name.com', '127.0.0.1', 120);
```

If you directly when you instantiate the class, it's no difference from the original package e.g.

```php
    use Cloudflare\Zone\Dns;

    // Create a connection to the Cloudflare API which you can
    // then pass into other services, e.g. DNS, later on
    $dns = new Cloudflare\Zone\Dns('email@example.com', 'API_KEY');
    $dns->create('12345678901234567890', 'TXT', 'name.com', '127.0.0.1', 120);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) run tests on [Codeception](http://codeception.com/) and relies [wp-browser](https://github.com/lucatume/wp-browser) to provide WordPress integration.
Before testing, you have to install WordPress locally and add a [codeception.yml](http://codeception.com/docs/reference/Configuration) file.
See [codeception.example.yml](codeception.example.yml).

Actually run the tests:

``` bash
$ vendor/bin/codecept build
$ vendor/bin/codecept run
```

We also test all PHP files against [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/).

With [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) installed, run:

``` bash
$ phpcs -p --standard=ruleset.xml;
```

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/cloudflare-wp-api/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email cloudflare-wp-api@typist.tech instead of using the issue tracker.

## Credits

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) is [Typist Tech](https://www.typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus).

Full list of contributors can be found [here](https://github.com/TypistTech/cloudflare-wp-api/graphs/contributors).

Special thanks to [James Bell](https://james-bell.co.uk/) whose [Cloudflare package](https://packagist.org/packages/jamesryanbell/cloudflare) make this project possible.

## License

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](./LICENSE) for more information.

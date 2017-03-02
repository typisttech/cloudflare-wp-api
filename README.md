# Cloudflare WP API

[![Latest Stable Version](https://poser.pugx.org/typisttech/cloudflare-wp-api/v/stable)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![Total Downloads](https://poser.pugx.org/typisttech/cloudflare-wp-api/downloads)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![Build Status](https://travis-ci.org/TypistTech/cloudflare-wp-api.svg?branch=master)](https://travis-ci.org/TypistTech/cloudflare-wp-api)
[![codecov](https://codecov.io/gh/TypistTech/cloudflare-wp-api/branch/master/graph/badge.svg)](https://codecov.io/gh/TypistTech/cloudflare-wp-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TypistTech/cloudflare-wp-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TypistTech/cloudflare-wp-api/?branch=master)
[![PHP Versions Tested](http://php-eye.com/badge/typisttech/cloudflare-wp-api/tested.svg)](https://travis-ci.org/TypistTech/cloudflare-wp-api)
[![StyleCI](https://styleci.io/repos/83097565/shield?branch=master)](https://styleci.io/repos/83097565)
[![Dependency Status](https://gemnasium.com/badges/github.com/TypistTech/cloudflare-wp-api.svg)](https://gemnasium.com/github.com/TypistTech/cloudflare-wp-api)
[![Latest Unstable Version](https://poser.pugx.org/typisttech/cloudflare-wp-api/v/unstable)](https://packagist.org/packages/typisttech/cloudflare-wp-api)
[![License](https://poser.pugx.org/typisttech/cloudflare-wp-api/license)](https://packagist.org/packages/typisttech/cloudflare-wp-api)

WordPress HTTP API replacement of the [jamesryanbell/cloudflare](https://packagist.org/packages/jamesryanbell/cloudflare) package.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Why use ``WP HTTP API`` instead of ``curl``?](#why-use-wp-http-api-instead-of-curl)
- [Install](#install)
- [Usage](#usage)
  - [Successful responses](#successful-responses)
  - [Error responses](#error-responses)
- [Start Developing](#start-developing)
- [Running the Tests](#running-the-tests)
- [Feedback](#feedback)
- [Change log](#change-log)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Why use ``WP HTTP API`` instead of ``curl``?

``curl`` is great. However, it is not always available on every hosts, especially shared hosting.
Using [WordPress HTTP API](https://developer.wordpress.org/plugins/http-api/) lets WordPress figure out the best way to make HTTP requests.
It could be ``curl`` or something else. You don't need to care about it. See [WordPress codex](https://codex.wordpress.org/HTTP_API).

## Install

Installation should be done via composer, details of how to install composer can be found at [https://getcomposer.org/](https://getcomposer.org/).

``` bash
$ composer require typisttech/cloudflare-wp-api
```

Since the [jamesryanbell/cloudflare](https://packagist.org/packages/jamesryanbell/cloudflare) package doesn't provide a way to inject client objects,
we have to rename ``Cloudflare\Api`` to ``Cloudflare\BaseApi``. And, use our [``Api``](src/Api.php) class instead 

``` bash
$ vendor/bin/cfwp build
```

You have to run the command on every ``composer install`` and ``composer update``.
A better way to do so is to add this command to ``composer.json`` like so:

``` json
  "scripts": {
    "post-install-cmd": "cfwp build",
    "post-update-cmd": "cfwp build",
    "pre-autoload-dump": "cfwp build"
  }
```


## Usage

Once ``$ cfwp build`` is done, you can use it exactly the same as the original package. 

See [jamesryanbell/cloudflare](https://github.com/jamesryanbell/cloudflare) for more details about the original package.

### Successful responses

Decode **body** array from ``wp_remote_request``.

### Error responses

``WP_Error`` object. Maybe returned from ``wp_remote_request``, or one of the followings:

| Code                  | Message                                       | Data      |
|:--------------------- |:--------------------------------------------- |:--------- |
| authentication-error  | Authentication information must be provided   |           |
| authentication-error  | Email is not valid                            |           |
| decode-error          | Response errors is not an array               | response  |

Or, one of the Coudlfare defined error codes, here is some example:

| Code  | Message                                                   | Data      |
|:----- |:--------------------------------------------------------- |:--------- |
| 1012  | Request must contain one of 'purge_everything' or 'files' | response  |
| 1210  | That operation is no longer allowed for that domain       | response  |

## Start Developing

This command will clone the project source code from GitHub and install its dependencies. 

``` bash
$ composer create-project --no-install --prefer-source --keep-vcs typisttech/cloudflare-wp-api:dev-master
$ cd cloudflare-wp-api
$ composer install
```

## Running the Tests

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) run tests on [Codeception](http://codeception.com/) and relies [wp-browser](https://github.com/lucatume/wp-browser) to provide WordPress integration.
Before testing, you have to install WordPress locally and add a [codeception.yml](http://codeception.com/docs/reference/Configuration) file.
See [codeception.example.yml](codeception.example.yml) for a [Varying Vagrant Vagrants](https://varyingvagrantvagrants.org/) configuration example.

Actually run the tests:

``` bash
$ composer test
```

We also test all PHP files against [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/).

Check the code style with ``$ composer check-style`` and fix it with ``$ composer fix-style``.

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/cloudflare-wp-api/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](.github/CONDUCT.md) for details.

## Security

If you discover any security related issues, please email cloudflare-wp-api@typist.tech instead of using the issue tracker.

## Credits

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) is a [Typist Tech](https://www.typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus).

Full list of contributors can be found [here](https://github.com/TypistTech/cloudflare-wp-api/graphs/contributors).

Special thanks to [James Bell](https://james-bell.co.uk/) whose [Cloudflare package](https://packagist.org/packages/jamesryanbell/cloudflare) make this project possible.

## License

[Cloudflare WP API](https://github.com/TypistTech/cloudflare-wp-api) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](LICENSE) for more information.

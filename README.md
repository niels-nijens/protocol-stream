# Protocol stream

[![Latest version on Packagist][icon-version]][link-version]
[![Software License][icon-license]](LICENSE.md)
[![Build Status][icon-build]][link-build]
[![Coverage Status][icon-coverage]][link-coverage]
[![SensioLabsInsight][icon-security]][link-security]
[![StyleCI][icon-code-style]][link-code-style]

A library to create stream wrappers in PHP.

## Installation using Composer

Run the following command to add the package to the composer.json of your project:

``` bash
$ composer require niels-nijens/protocol-stream
```

## Usage

``` php
<?php

$stream = new Stream('stream', array('/allowed/path'));

StreamManager::create()->registerStream($stream);

readfile('stream://file-in-allowed-path.ext');

```

## Author

- [Niels Nijens][link-author]

Also see the list of [contributors][link-contributors] who participated in this project.

## License

This project licensed under the MIT License. Please see the [LICENSE file](LICENSE.md) for details.

[icon-version]: https://img.shields.io/packagist/v/niels-nijens/protocol-stream.svg
[icon-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[icon-build]: https://travis-ci.org/niels-nijens/protocol-stream.svg?branch=master
[icon-coverage]: https://coveralls.io/repos/niels-nijens/protocol-stream/badge.svg?branch=master
[icon-security]: https://img.shields.io/sensiolabs/i/fce7d113-901e-44bb-8b02-d7a62d8a2c70.svg
[icon-code-style]: https://styleci.io/repos/50087926/shield?style=flat

[link-version]: https://packagist.org/packages/niels-nijens/protocol-stream
[link-build]: https://travis-ci.org/niels-nijens/protocol-stream
[link-coverage]: https://coveralls.io/r/niels-nijens/protocol-stream?branch=master
[link-security]: https://insight.sensiolabs.com/projects/fce7d113-901e-44bb-8b02-d7a62d8a2c70
[link-code-style]: https://styleci.io/repos/50087926
[link-author]: https://github.com/niels-nijens
[link-contributors]: https://github.com/niels-nijens/protocol-stream/contributors

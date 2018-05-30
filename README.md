<img src="docs/leviy-logo.png" alt="LEVIY logo" align="right" />

# Release Tool

Inspired by [Liip RMT](https://github.com/liip/RMT), this release tool helps you
to automate the release of new versions of your software. It is written in PHP
but can be used for any type of project, as long as you have PHP installed on
your machine.

[![Build status](https://img.shields.io/travis/leviy/release-tool.svg)](https://travis-ci.com/leviy/release-tool)
[![License](https://img.shields.io/github/license/leviy/release-tool.svg)](https://github.com/leviy/release-tool/blob/master/LICENSE.txt)

## Installation

Install this package using [Composer](https://getcomposer.org/):

```bash
composer require --dev leviy/release-tool
```

## Usage

### Releasing a new version

Use ```vendor/bin/release release <version>``` to release a version. For example:

```bash
vendor/bin/release release 1.0.0
```

This will release version 1.0.0. By default, this will create a prefixed,
annotated Git tag, in this case `v1.0.0`.

After tagging a first version, you can let the tool calculate the new version
number for you based on the current version and a number of questions. To do so,
omit the version from the previous command:

```bash
vendor/bin/release release
```

### Other commands

Run ```vendor/bin/release list``` to see a list of available commands.

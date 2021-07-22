<img src="docs/logo.svg" width="200" alt="Leviy logo" align="right" />

# Release Tool

Inspired by [Liip RMT](https://github.com/liip/RMT), this release tool helps you
to automate the release of new versions of your software. It is written in PHP
but can be used for any type of project, as long as you have PHP installed on
your machine.

[![Test](https://github.com/leviy/release-tool/workflows/Test/badge.svg)](https://github.com/leviy/release-tool/actions?query=workflow%3ATest)
[![License](https://img.shields.io/github/license/leviy/release-tool.svg)](https://github.com/leviy/release-tool/blob/master/LICENSE.txt)
[![GitHub release](https://img.shields.io/github/release/leviy/release-tool.svg)](https://github.com/leviy/release-tool/releases/latest)
[![Required PHP version](https://img.shields.io/packagist/php-v/leviy/release-tool.svg)](https://github.com/leviy/release-tool/blob/master/composer.json)

## Features

- Determines the next version number based on
  [semantic versioning](https://semver.org/)
- Creates an annotated Git tag and pushes it to the remote repository
- Creates a GitHub release with the name of the release and a changelog with
  changes since the previous version
- Supports pre-release (alpha/beta/rc) versions

## Installation

### Phar (recommended)
The recommended method of installing this package is using a phar file. This is because installing using Composer can possibly cause dependency conflicts. You can download the most recent phar from the [Github Releases](https://github.com/leviy/release-tool/releases/latest) page.

### Execute with Phar
After downloading the `release-tool.phar` file, Put the `release-tool.phar` file in your home/bin (`~`/bin) folder. After that remove the `.phar` at the end of the file. Next you want to give the right permissions to the `release-tool`. Execute the following command in your console so that the `release-tool` has the correct permissions:

```bash
chmod 775 release-tool
```

### Composer
Alternatively, you can install this package using [Composer](https://getcomposer.org/):

```bash
composer require --dev leviy/release-tool
```

## Configuration

### GitHub personal access token

This tool requires a personal access token with `repo` scope to create GitHub
releases. Create one [here](https://github.com/settings/tokens/new?scopes=repo&description=Leviy+Release+Tool)
and store it in `.release-tool/auth.yml` in your home folder (`~` on Linux, user
folder on Windows):

```yml
credentials:
  github:
    token: <token>
```

## Usage

> Note: these usage instructions assume that you have downloaded the
> `release-tool.phar` file to your project directory. If you have installed it
> in a different location, update the commands accordingly. If you have
> installed the tool as a Composer dependency, use `vendor/bin/release` instead.

### Releasing a new version

Use ```release-tool.phar release <version>``` to release a version. For example:

```bash
release-tool.phar release 1.0.0
```

This will release version 1.0.0. By default, this will create a prefixed,
annotated Git tag, in this case `v1.0.0`.

#### Automatically generating a version number

After tagging a first version, you can let the tool calculate the new version
number for you based on the current version and a number of questions. To do so,
omit the version from the previous command:

```bash
release-tool.phar release
```

#### Pre-release versions

If you want to create a pre-release (alpha/beta/rc) version, run:

```bash
release-tool.phar release --pre-release
```

### Other commands

Run ```release-tool.phar list``` to see a list of available commands.

## Updating the release tool

The following command will update the release tool to the latest version:

```bash
release-tool.phar self-update
```

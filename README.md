<img src="docs/leviy-logo.png" alt="LEVIY logo" align="right" />

# Release Tool

Inspired by [Liip RMT](https://github.com/liip/RMT), this release tool helps you
to automate the release of new versions of your software. It is written in PHP
but can be used for any type of project, as long as you have PHP installed on
your machine.

[![Build status](https://travis-ci.com/leviy/release-tool.svg)](https://travis-ci.com/leviy/release-tool)
[![License](https://img.shields.io/github/license/leviy/release-tool.svg)](https://github.com/leviy/release-tool/blob/master/LICENSE.txt)
[![GitHub release](https://img.shields.io/github/release/leviy/release-tool.svg)](https://github.com/leviy/release-tool/releases/latest)
[![Required PHP version](https://img.shields.io/packagist/php-v/leviy/release-tool.svg)](https://github.com/leviy/release-tool/blob/master/composer.json)

## Installation


### Phar
The recommended method of installing this package is using a PHAR file. This is because installing using Composer can possibly cause dependency conflicts. You can download the most recent phar from the [Github Releases](https://github.com/leviy/release-tool/releases/latest) page.

### Composer
Alternatively, you can install this package using [Composer](https://getcomposer.org/):

```bash
composer require --dev leviy/release-tool
```

## Configuration

### Configuring GitHub (one-time step)

First create a Personal Access Token on Github so that the release tool can use the GitHub API.
You can get a token by clicking [here](https://github.com/settings/tokens/new?scopes=repo&description=Leviy+Release+Tool) and entering your GitHub password to prefill the fields in the form,
otherwise you can press the `generate new token` button on [this page](https://github.com/settings/tokens/) and allow everything inside the repo scope.

Next create a directory `.release-tool` inside your home folder (`~` on linux, user folder on windows).
Inside that folder create an `auth.yml` file with the following contents:

```yml
credentials:
  github:
    token: <github_token_from_previous_step>
```

For example:
`~/.release-tool/auth.yml`
```yml
credentials:
  github:
    token: 088qqr97753f5nez7o85ywcp8owagmd61p9qg1mc
```

The release tool is now configured to be able to work with GitHub on this PC!

## Usage

### Releasing a new version

Use ```vendor/bin/release release <version>``` to release a version. For example:

```bash
vendor/bin/release release 1.0.0
```

This will release version 1.0.0. By default, this will create a prefixed,
annotated Git tag, in this case `v1.0.0`.

#### Automatically generating a version number

After tagging a first version, you can let the tool calculate the new version
number for you based on the current version and a number of questions. To do so,
omit the version from the previous command:

```bash
vendor/bin/release release
```

#### Pre-release versions

If you want to create a pre-release (alpha/beta/rc) version, run:

```bash
vendor/bin/release release --pre-release
```

### Other commands

Run ```vendor/bin/release list``` to see a list of available commands.

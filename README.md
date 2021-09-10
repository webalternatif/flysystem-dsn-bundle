# Flysystem DSN bundle

[![Source code](https://img.shields.io/badge/source-GitHub-blue)](https://github.com/webalternatif/flysystem-dsn-bundle)
[![Software license](https://img.shields.io/github/license/webalternatif/flysystem-dsn-bundle)](https://github.com/webalternatif/flysystem-dsn-bundle/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/webalternatif/flysystem-dsn-bundle)](https://github.com/webalternatif/flysystem-dsn-bundle/issues)
[![Test status](https://img.shields.io/github/workflow/status/webalternatif/flysystem-dsn-bundle/test?label=tests)](https://github.com/webalternatif/flysystem-dsn-bundle/actions/workflows/test.yml)
[![Psalm coverage](https://shepherd.dev/github/webalternatif/flysystem-dsn-bundle/coverage.svg)](https://psalm.dev)
[![Psalm level](https://shepherd.dev/github/webalternatif/flysystem-dsn-bundle/level.svg)](https://psalm.dev)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/webalternatif/flysystem-dsn-bundle/master)](https://infection.github.io)

This bundle integrates the [Flysystem DSN][1] library with Symfony, allowing the
creation of adapters as services with DSN from the configuration.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter][2] of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require webalternatif/flysystem-dsn-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following
command to download the latest stable version of this bundle:

```console
$ composer require webalternatif/flysystem-dsn-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the
`config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Webf\Flysystem\DsnBundle\WebfFlysystemDsnBundle::class => ['all' => true],
];
```

## Usage

Adapters are configured under the `webf_flysystem_dsn.adapters` Symfony config
path, and are then available as services with id
`webf_flysystem_dsn.adapter.{name}`:

```yaml
webf_flysystem_dsn:
    adapters:
        adapter1: '%env(STORAGE1_DSN)%' # service: webf_flysystem_dsn.adapter.adapter1
        adapter2: '%env(STORAGE2_DSN)%' # service: webf_flysystem_dsn.adapter.adapter2
```

See [available adapters][3] section of `webalternatif/flysystem-dsn` to know
what DSN you can use.

### Using your own DSN

If you want to use your own DSN to build your own Flysystem adapters, you can
create an adapter factory service that implement
`Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface`.

To register the factory, either you have [autoconfiguration][4] enabled, or you
have to tag your service with `webf_flysystem_dsn.adapter_factory` (also
available in PHP with
`Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension::ADAPTER_FACTORY_TAG_NAME`).

## Tests

To run all tests, execute the command:

```bash
$ composer test
```

This will run [Psalm][5], [PHPUnit][6], [Infection][7] and a [PHP-CS-Fixer][8]
check, but you can run them individually like this:

```bash
$ composer psalm
$ composer phpunit
$ composer infection
$ composer cs-check
```

[1]: https://github.com/webalternatif/flysystem-dsn
[2]: https://getcomposer.org/doc/00-intro.md
[3]: https://github.com/webalternatif/flysystem-dsn#adapters
[4]: https://symfony.com/doc/current/service_container.html#the-autoconfigure-option
[5]: https://psalm.dev
[6]: https://phpunit.de
[7]: https://infection.github.io
[8]: https://cs.symfony.com/

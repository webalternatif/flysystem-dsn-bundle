# Flysystem DSN bundle

[![Source code](https://img.shields.io/badge/source-GitHub-blue)](https://github.com/webalternatif/flysystem-dsn-bundle)
[![Packagist Version](https://img.shields.io/packagist/v/webalternatif/flysystem-dsn-bundle)](https://packagist.org/packages/webalternatif/flysystem-dsn-bundle)
[![Software license](https://img.shields.io/github/license/webalternatif/flysystem-dsn-bundle)](https://github.com/webalternatif/flysystem-dsn-bundle/blob/main/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/webalternatif/flysystem-dsn-bundle)](https://github.com/webalternatif/flysystem-dsn-bundle/issues) \
[![Test status](https://img.shields.io/github/actions/workflow/status/webalternatif/flysystem-dsn-bundle/test.yml?branch=main&label=tests)](https://github.com/webalternatif/flysystem-dsn-bundle/actions/workflows/test.yml)
[![Psalm coverage](https://shepherd.dev/github/webalternatif/flysystem-dsn-bundle/coverage.svg)](https://psalm.dev)
[![Psalm level](https://shepherd.dev/github/webalternatif/flysystem-dsn-bundle/level.svg)](https://psalm.dev)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/webalternatif/flysystem-dsn-bundle/main)](https://infection.github.io)

This bundle integrates the [Flysystem DSN][1] library with Symfony, allowing the
creation of adapters as services with DSN from the configuration.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter][2] of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require webalternatif/flysystem-dsn-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following
command to download the latest stable version of this bundle:

```console
composer require webalternatif/flysystem-dsn-bundle
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

In addition to [available adapters][3] from `webalternatif/flysystem-dsn`, this
bundle provides a Symfony-specific DSN `service://service_id` to which you must
provide an identifier that references an external service (that must be a
Flysystem adapter). It could be useful if you already have adapter services,
and you want to inject them into a composed adapter like `failover`:
`failover(service://external_service_id ...)`.

### Integration with Flysystem bundles

As explained above, this bundle only provides services that are Flysystem
adapters, but they're not usable as is. Generally you'll have to use another
bundle that provide `FilesystemOperator` instances.

The two best known are [`oneup/flysystem-bundle`][4] and
[`league/flysystem-bundle`][5], here is some examples of configuration for those
two bundles (considering the `webf_flysystem_dsn` configuration above).

#### `oneup/flysystem-bundle`

```yaml
oneup_flysystem:
    adapters:
        adapter1:
            custom:
                service: webf_flysystem_dsn.adapter.adapter1
        adapter2:
            custom:
                service: webf_flysystem_dsn.adapter.adapter2

    filesystems:
        storage1:
            adapter: adapter1
        storage2:
            adapter: adapter2
```

#### `league/flysystem-bundle`

```yaml
flysystem:
    storages:
        storage1:
            adapter: webf_flysystem_dsn.adapter.adapter1
        storage2:
            adapter: webf_flysystem_dsn.adapter.adapter2
```

### Integration with [`webalternatif/flysystem-failover-bundle`][6]

If [`webalternatif/flysystem-failover-bundle`][6] is installed, the
[`failover`][7] DSN function becomes available and all configured failover
adapters are registered so that they can be used in `webf:flysystem-failover:*`
Symfony commands.

#### Using `failover` DSN function nested in others

In order to use the `failover` DSN function as parameter of other DSN functions,
adapters created by the corresponding factories must implement
`CompositeFilesystemAdapter` from [`webalternatif/flysystem-composite`][8].
Without that, the bundle wouldn't be able to register them, and they won't be
usable in `webf:flysystem-failover:*` Symfony commands.

### Using your own DSN

If you want to use your own DSN to build your own Flysystem adapters, you can
create an adapter factory service that implement
`Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface`.

To register the factory, either you have [autoconfiguration][9] enabled, or you
have to tag your service with `webf_flysystem_dsn.adapter_factory` (also
available in PHP with
`Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension::ADAPTER_FACTORY_TAG_NAME`).

## Tests

To run all tests, execute the command:

```bash
composer test
```

This will run [Psalm][10], [PHPUnit][11], [Infection][12] and a [PHP-CS-Fixer][13]
check, but you can run them individually like this:

```bash
composer psalm
composer phpunit
composer infection
composer cs-check
```

[1]: https://github.com/webalternatif/flysystem-dsn
[2]: https://getcomposer.org/doc/00-intro.md
[3]: https://github.com/webalternatif/flysystem-dsn#adapters
[4]: https://github.com/1up-lab/OneupFlysystemBundle
[5]: https://github.com/thephpleague/flysystem-bundle
[6]: https://github.com/webalternatif/flysystem-failover-bundle
[7]: https://github.com/webalternatif/flysystem-dsn#failover
[8]: https://github.com/webalternatif/flysystem-composite
[9]: https://symfony.com/doc/current/service_container.html#the-autoconfigure-option
[10]: https://psalm.dev
[11]: https://phpunit.de
[12]: https://infection.github.io
[13]: https://cs.symfony.com/

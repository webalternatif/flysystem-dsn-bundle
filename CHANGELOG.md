## v0.5.1 (unreleased)

### ✨ New features

* Add compatibility with `psr/container:^2.0`
* Add support of PHP 8.2

## v0.5.0 (January 16, 2023)

### 💥 Breaking changes

* Replace league/flysystem-sftp by league/flysystem-sftp-v3 ([#2](https://github.com/webalternatif/flysystem-dsn-bundle/pull/2))
* Bump `webalternatif/flysystem-dsn` version to `^0.5.0` ([#2](https://github.com/webalternatif/flysystem-dsn-bundle/pull/2))

## v0.4.1 (June 14, 2022)

### ✨ New features

* Allow Symfony components `^6.0` ([a0b7f47](https://github.com/webalternatif/flysystem-dsn-bundle/commit/a0b7f47dd67e34bfe0d5c84e7259c0ae8b203ca2))

## v0.4.0 (June 13, 2022)

### 💥 Breaking changes

* Bump dependencies ([169b8ef](https://github.com/webalternatif/flysystem-dsn-bundle/commit/169b8efd2444cb8654e12abcd5c132b84aa297af))

## v0.3.5 (December 30, 2021)

### ✨ New features

* Add support of PHP 8.1 ([9efb764](https://github.com/webalternatif/flysystem-dsn-bundle/commit/9efb764cb467e87962fe37ae5896ae029c645ccd))

## v0.3.4 (November 19, 2021)

### 🐛 Bug fixes

* Fix container compilation when FlysystemFailoverBundle is not installed ([d15f61a](https://github.com/webalternatif/flysystem-dsn-bundle/commit/d15f61adae279b87e677aa81f1fc86536ee78219))

## v0.3.3 (November 7, 2021)

### ✨ New features

* Allow `failover` DSN to be nested in other DSN functions ([e5aa638](https://github.com/webalternatif/flysystem-dsn-bundle/commit/e5aa6384aed2eb41d3f13a0f575f9cf2a440f42f))
* Bump `webalternatif/flysystem-dsn` version to `^0.3.1` ([ce66ed7](https://github.com/webalternatif/flysystem-dsn-bundle/commit/ce66ed7d6d346d2cd60ddd067a73aac0fa532095))
* Register `FtpAdapterFactory` as service ([30309e2](https://github.com/webalternatif/flysystem-dsn-bundle/commit/30309e225340c58b3f5b971beb51a6d215ff7e33))
* Register `SftpAdapterFactory` as service ([15b2148](https://github.com/webalternatif/flysystem-dsn-bundle/commit/15b21483550c16ddddb615aa78bc20b242d87b5f))

## v0.3.2 (October 8, 2021)

### 🐛 Bug fixes

* Do not register adapter factory services if underlying adapter classes does not exist ([890185f](https://github.com/webalternatif/flysystem-dsn-bundle/commit/890185fc0bf52f3d820b804b793ce000ba23b095))

## v0.3.1 (October 8, 2021)

### ✨ New features

* Register `InMemoryAdapterFactory` as service ([44ed8d7](https://github.com/webalternatif/flysystem-dsn-bundle/commit/44ed8d7ca5c0cd31a515be94f253087558859a67))
* Register `LocalAdapterFactory` as service ([4e59902](https://github.com/webalternatif/flysystem-dsn-bundle/commit/4e59902d876b19b67849a8477aa7bf19a73e6763))

## v0.3.0 (October 8, 2021)

### 💥 Breaking changes

* Bump `webalternatif/flysystem-dsn` version to `^0.3.0` ([b693fb0](https://github.com/webalternatif/flysystem-dsn-bundle/commit/b693fb040157531c74fa7d975f1404a6cb309817))

### ✨ New features

* Register `FlysystemAdapterFactoryInterface` as alias of service `webf_flysystem_dsn.adapter_factory` ([07ebc45](https://github.com/webalternatif/flysystem-dsn-bundle/commit/07ebc4545a73cb1e0ae8e928b0b7c9713cab1991))

## v0.2.0 (September 14, 2021)

### 💥 Breaking changes

* Bump `webalternatif/flysystem-dsn` version to `^0.2.0` ([be0206b](https://github.com/webalternatif/flysystem-dsn-bundle/commit/be0206b746db7b37bde6a973846304f9e5aa1770))

### ✨ New features

* Add `ServiceAdapterFactory` ([be0206b](https://github.com/webalternatif/flysystem-dsn-bundle/commit/be0206b746db7b37bde6a973846304f9e5aa1770))
* Register `FailoverAdapterFactory` as service ([bdc3175](https://github.com/webalternatif/flysystem-dsn-bundle/commit/bdc31756d5fd88e42e0c6c8c27ffd9d28c43c970))

## v0.1.0 (August 31, 2021)

First version. ([b45f69a](https://github.com/webalternatif/flysystem-dsn-bundle/commit/b45f69a82565def5feaea21b2ef38e6824ce6401))

## v0.3.3 (November 7, 2021)

### ✨ New features

* Allow `failover` DSN to be nested in other DSN functions
* Bump `webalternatif/flysystem-dsn` version to `^0.3.1`
* Register `FtpAdapterFactory` as service
* Register `SftpAdapterFactory` as service

## v0.3.2 (October 8, 2021)

### 🐛 Bug fixes

* Do not register adapter factory services if underlying adapter classes does not exist

## v0.3.1 (October 8, 2021)

### ✨ New features

* Register `InMemoryAdapterFactory` as service
* Register `LocalAdapterFactory` as service

## v0.3.0 (October 8, 2021)

### 💥 Breaking changes

* Bump `webalternatif/flysystem-dsn` version to `^0.3.0`

### ✨ New features

  * Register `FlysystemAdapterFactoryInterface` as alias of service `webf_flysystem_dsn.adapter_factory`

## v0.2.0 (September 14, 2021)

### 💥 Breaking changes

  * Bump `webalternatif/flysystem-dsn` version to `^0.2.0`

### ✨ New features

  * Add `ServiceAdapterFactory`
  * Register `FailoverAdapterFactory` as service

## v0.1.0 (August 31, 2021)

First version.

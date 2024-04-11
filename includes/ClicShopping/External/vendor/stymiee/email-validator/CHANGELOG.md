# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.4] - 2024-04-09

### Changed
- CHANGELOG format

### Fixed
- Issue #5: Static variables prevents running validation with different configurations
- Issue #6: `googlemail.com` is now recognized as a Gmail address
- Issue #6: `.` are now removed when sanitizing Gmail addresses (to get to the root email address)


## [1.1.3] - 2022-10-12

### Fixed

- Handled potential for null being returned when validating a banned domain name


## [1.1.1] - 2022-10-11

### Changed 

- Banned domain check to use pattern matching for more robust validation including subdomains


## [1.1.1] - 2022-02-22

### Fixed

- When getting an email address' username, if there was none, return an empty string instead of NULL


## [1.1.0] - 2022-02-02

### Added 

- Support for identifying and working with Gmail addresses using the "plus trick" to create unique addresses


## [1.0.2] - 2022-01-24

### Fixed

- Issue #2: Error state not clearing between validations


## [1.0.1] - 2021-09-20

### Added

- Pull Request #1: Added EmailValidator::getErrorCode()


## [1.0.0] - 2020-08-02

 - Initial release

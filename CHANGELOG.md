# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased
### Removed
- #6 illuminate/filesystem from composer.json

## [0.0.14] - 2017-11-23
### Changed
- Updated laravel-zero/framework for friendlier error messages
### Removed
- composer.lock

## [0.0.13] - 2017-11-22
### Added
- server:fix -- fixes crashed servers - **scheduled**

## [0.0.12] - 2017-11-17
### Fixed
- A totally not obvious copy-paste job

## [0.0.11] - 2017-11-17
### Added
- server:backup -- backs up a server
- server:delete -- deletes server after running a server backup unless you use `--no-backup`
### Fixed
- fivem:update -- some dev testing code was left in

## [0.0.10] - 2017-11-17
### Changed
- almost all commands, cleaning up
### Fixed
- server:say -- slight issue

## [0.0.9] - 2017-11-16
### Added
- server:console -- see the server console
- server:say -- send a message to the console
### Changed README
- Added support
### Fixed
- server:list -- now checks status of server process
- server:create -- shut git the fu*k up
- server:path -- now tells you the path if it is set and a paht isn't provided
- server:stop -- now sends message to server, disablable

## [0.0.8] - 2017-11-15
### Fixed
- Stop Command signature throwing error

## [0.0.7] - 2017-11-15
### Changed
- Storage location for easier updating

## [0.0.4] - 2017-11-15
### Added
- Laravel-Zero
- The initial commands

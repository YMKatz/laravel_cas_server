# CAS Server for Laravel backed by LDAP

laravel_cas_server_ldap is a Laravel package that implements the server part of [CAS protocol](https://apereo.github.io/cas/4.2.x/protocol/CAS-Protocol-Specification.html) v1/v2/v3. This is a fork of [leo108/laravel_cas_server](https://github.com/leo108/laravel_cas_server) that is backed by LDAP instead of Eloquent.

This package works for Laravel 5.5/5.6 . This fork does not support Laravel 5.1 - 5.4 .

[![Latest Version](http://img.shields.io/github/release/YMKatz/laravel_cas_server_ldap.svg)](https://github.com/YMKatz/laravel_cas_server_ldap/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://img.shields.io/travis/YMKatz/laravel_cas_server_ldap/master.svg)](https://travis-ci.org/YMKatz/laravel_cas_server_ldap)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/YMKatz/laravel_cas_server_ldap/master.svg)](https://scrutinizer-ci.com/g/YMKatz/laravel_cas_server_ldap/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/YMKatz/laravel_cas_server_ldap.svg)](https://packagist.org/packages/YMKatz/laravel_cas_server_ldap)

## Requirements

- PHP >=7.0
- Adldap-Laravel

## Installation && Usage

- `composer require ymkatz/laravel_cas_server_ldap`
- `php artisan vendor:publish --provider="YMKatz\CAS\CASServerServiceProvider"`
- modify `config/cas.php`, fields in config file are all self-described
- make your `App\User` implement `YMKatz\CAS\Contracts\Models\UserModel`
- create a class implements `YMKatz\CAS\Contracts\TicketLocker`
- create a class implements `YMKatz\CAS\Contracts\Interactions\UserLogin`
- visit `http://your-domain/cas/login` to see the login page (assume that you didn't change the `router.prefix` value in `config/cas.php`)

## Example

If you are looking for an out of box solution of CAS Server powered by PHP, you can check [php_cas_server](https://github.com/leo108/php_cas_server)

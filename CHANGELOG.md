# Changelog

All notable changes to `laravel-docuware` will be documented in this file.

## 0.2.0 - 2021-04-19

- Authentication is completely handled by the package now. No need to login
  (`DocuWare::login`) or logout (`DocuWare::logout`).
- Added new environment variable **cookie_lifetime** to overwrite the lifetime 
  of the authentication cookie.

## 0.1.0 - 2021-04-06

- DTO fake methods added

## 0.0.0 - 2021-04-06

- initial release

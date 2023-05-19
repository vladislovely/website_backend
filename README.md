<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Project
It is laravel based project, developed for backend part of my site.

## Requirements

- PHP 8.2
- Docker

## Tech Stack
**Server:** PHP 8.2, Docker, Mailpit, Mysql 8.0, Laravel 10, Memcached

## Environment Variables

To run this project, you will need to create s3 storage and add the following environment variables to your .env file

`AWS_ENDPOINT`

`AWS_ACCESS_KEY_ID`

`AWS_SECRET_ACCESS_KEY`

`AWS_DEFAULT_REGION`

`AWS_BUCKET`


## Installation
```bash
  composer install
  cp .env.example .env
  alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
  sail up -d
```

### Info
Service port - 80
Mailpit port - 8025
Telescope route - localhost/telescope

### Helpers
For easy use sail, u can copy this alias - <b>alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'</b>

## License

[MIT](https://choosealicense.com/licenses/mit/)

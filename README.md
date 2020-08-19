# blog-backend-symfony

![API Tests](https://github.com/qbbr/blog-backend-symfony/workflows/API%20Tests/badge.svg)

Blog REST API on Symfony 5

see [blog-frontend-vuejs](https://github.com/qbbr/blog-fontend-vuejs)

## depends

 * [php 7.4](https://www.php.net/)
 * [composer](https://getcomposer.org/download/)
 * [symfony cli](https://symfony.com/download)
 * [openssl](https://www.openssl.org/) for JWT

## run

```
make generate-jwt-keys
make init-db
symfony server:start
```

see [rest-api.http](rest-api.http) (IntelliJ IDEA HTTP Client file)

## tests

```bash
make install
make test
```

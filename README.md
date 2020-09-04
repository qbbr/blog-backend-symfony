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
make install
make generate-jwt-keys
make init-db
symfony server:start
```

see [rest-api.http](rest-api.http) (IntelliJ IDEA HTTP Client file)

## routes

| route               | method     | description                      | JWT required |
|---------------------|------------|----------------------------------|--------------|
| /register/          | POST       | create new user                  |              |
| /login/             | POST       | login                            |              |
| /user/profile/      | GET        | get user profile                 | Y            |
| /user/profile/      | PUT, PATCH | update user profile              | Y            |
| /user/profile/      | DELETE     | delete user profile              | Y            |
| /posts/             | GET        | get all posts \w pagination      |              |
| /post/{slug}/       | GET        | get post by slug                 |              |
| /tags/              | GET        | get all tags                     |              |
| /user/posts/        | GET        | get all user posts \w pagination | Y            |
| /user/posts/        | DELETE     | delete all user posts            | Y            |
| /user/post/         | POST       | create user post                 | Y            |
| /user/post/{id}/    | GET        | get user post by id              | Y            |
| /user/post/{id}/    | PUT, PATCH | update user post by id           | Y            |
| /user/post/{id}/    | DELETE     | delete user post by id           | Y            |
| /user/post/md2html/ | POST       | convert markdown to html         | Y            |

## tests

```bash
make install
make test
```

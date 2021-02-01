Symfony OAuth2
==============
Symfony with implementation of OAuth2 protocol
-------------------------------------------------------------

### What this project contains?
This is just a simple configuration of Symfony 5 application, with OAuth2 used for authorization. For OAuth2, I used [trikoder/oauth2-bundle](https://github.com/trikoder/oauth2-bundle) library.

### Project assumptions
This project was created to enable a simple start with Symfony and OAuth2 protocol.

There are two application scopes: User API and Admin API. 
There are also two separate OAuth clients to simulate real life application, because this separation is the most common one.

I use Domain Driven Development as project architecture and Command Query Responsibility Separation with Symfony Messenger. 

### Project stack

* PHP8
* Symfony 5
* Postgres 12
* Nginx

### How to run this project

**1. Build the project**
```bash
docker-compose build
```

**2. Run the project**
```bash
docker-compose up -d
```
>If you are using MacOs, you can start Mutagen for better development performance.
> ```bash
> mutagen project start
> ```

**3. Generate RSA keys for JWT**
```bash
docker-compose exec php bin/generate-keys
```

**4. Install Composer dependencies**
```bash
docker-compose exec php composer install
```

**5. Apply database migrations**
```bash
docker-compose exec php bin/console doctrine:migrations:migrate -n
```

**6. Now you can create OAuth2 clients**
```bash
docker-compose exec php bin/console trikoder:oauth2:create-client --scope client_api --grant-type password react-client-app
docker-compose exec php bin/console trikoder:oauth2:create-client --scope admin_api --grant-type password react-admin-app
```                                          
Example output
```bash                                                                             
 [OK] New oAuth2 client created successfully.                                         

 ------------------ ---------------------------------------------------------------------------------------------------------------------------------- 
  Identifier         Secret                                                                                                                            
 ------------------ ---------------------------------------------------------------------------------------------------------------------------------- 
  react-client-app   750cf4114c889fc902da0253c1e5f87fee464ff41c9033c4904e598a7746f0b2dd3981fb62b2b2b6f7571e515c95e09a065e091ef922488eb4729571b1c3a1d8  
 ------------------ ----------------------------------------------------------------------------------------------------------------------------------
 ```
> Tip: You can save secrets for further usage.
> 
> You also will find them in the `oauth2_client` table.
 
> Another tip: If the secret cannot be stored securely, you can set `client_secret` to `NULL`. This often happens when you will send requests directly from user application, e.g. React or Angular application, and user can check request parameters in browser console. The `client_secret` is no longer secret, so you can omit it.
> 
> Read more: [https://developer.okta.com/blog/2018/06/29/what-is-the-oauth2-password-grant](https://developer.okta.com/blog/2018/06/29/what-is-the-oauth2-password-grant)

In my examples I will omit `client_secret` to keep requests short and simple.

**7. Register a user**

Now you can register user with simple http request:
```bash
curl --location --request POST 'http://localhost/user' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "example@user.com",
    "password": "zaq12wsx"
}'
```

In response, you should get `201 Created` code, and empty response body.

> Tip: You should protect this endpoint, from bots and accidental registration, in the "real app".

**8. Logging into `react-client-app` with OAuth2:**

```bash
curl --location --request POST 'localhost/token' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'grant_type=password' \
--data-urlencode 'client_id=react-client-app' \
--data-urlencode 'username=example@user.com' \
--data-urlencode 'password=zaq12wsx'
```

> Note that, this is `application/x-www-form-urlencoded` request. This is the part of the OAuth2 protocol.

In response you will get `access_token` and `refresh_token`:

```bash

```

Symfony OAuth2
==============
Symfony with implementation of OAuth2 protocol
-------------------------------------------------------------
[![codecov](https://codecov.io/gh/BartoszBartniczak/Symfony-Oauth2/branch/master/graph/badge.svg?token=58XR3HWP2A)](https://codecov.io/gh/BartoszBartniczak/Symfony-Oauth2)
-----

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

```json
{
    "token_type": "Bearer",
    "expires_in": 3600,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJyZWFjdC1jbGllbnQtYXBwIiwianRpIjoiZTBkOGQ4ODNmMzM2NDI0OGM5YjMyMmQzOGZjYjgyMWRjNjg4MWQzZGNkYTg2NzVlZDgwYTk5NzU0OTQ0MDFkYWMwOTk5ZTEwOWVkNDgyZTEiLCJpYXQiOiIxNjEyNDcyNjY0Ljg4Nzk2MCIsIm5iZiI6IjE2MTI0NzI2NjQuODg3OTcyIiwiZXhwIjoiMTYxMjQ3NjI2NC43Njk2MjYiLCJzdWIiOiJleGFtcGxlQHVzZXIuY29tIiwic2NvcGVzIjpbImNsaWVudF9hcGkiXX0.AY6V9UtaqodfinhWiYB_pFppbFtAKdWYO4tofH84HxmCcpgcsD67pjEYqDnsYo2cSn-uxWhXYBuEN7w6yf6Gockral1eWTvaJLGSgPxYTfklrx7iy2h7TOtuLmI8QHnWBEFOoreE-VNhxHu5OjLauxMLldMnqT_ryBF3Je_ZaUm08GcVfVEur5gd3dkweeVlFUOdD2sirOkvO6s2_fYzmI9bj1SCUvv29OgYCobhTGZ_R9Ifi8U8BB_J7jCChosbZhdoMYCCryW7FrPqcrQP7dDrabCmLgPNoyF2dc9Yc65VYXOpefrJc0tZSaWeJAVMQWFZ3pUvbD-nucYTK9Xm8w",
    "refresh_token": "def50200bcdec580450dc6fec603bfcef08cc8b6d4ff9fe4f03bf835004dd3fa0195cca088991c89a17353924467b3f47fee31d289ac606d262033c256fe5075f0e792ef39e6dbf89833716e6088d36d9fc01b464731316e6160010e500b7f053a4994a088780e921168d6bc1ebade7abd622319bdc6b7ac9033034b10372cb32ea347dc6a9b9085030d0a083cee5dc436f0835117e10cb0aa895918cbf853c84db52f5fcaafd3ede28c537fec9e378673b26e626f0e3aa1febd50fe386222f14073f88979f8a12f4b4e490362b03dc254e252a89375a3e57648e4536f26bb8b03c47d01ad604773b07a55025008a1ebed4d8cec8f72d3367536b73346ac0fbd6bc647acd67086d8726dbe4fe24244ac66dfdeadad886ff9eb59d491ff413f0a7f6cda4820659022c61e6ff58ec94e09852871e6843d0658d795c1976f01849695019aa96ca51e679d6d6de73c4d9e1585a296908d836a204b30493ebcaea722eed1f460afb628385e7a3dce7121fa735299d242aa29e9f9e2c2cdcb326d83240c8c815039db9b947263424686"
}
```

**9. Send JSON request to protected endpoint**

Now you can request for logged in user profile, using Bearer token (from previous response):

```bash
curl --location --request GET 'http://localhost/user' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJyZWFjdC1jbGllbnQtYXBwIiwianRpIjoiYjcwMjBiNGZlNjNmODZiY2IzNDI5NmQ2ZTU1YjZjNjZjY2E4NjM3ODRiYjEwZjUzYWY1NzBhZTg5NWNhNzE1MjU2ZWM5MWM5Njk2NTA4MWUiLCJpYXQiOiIxNjEyNDcyODQ0LjY0MzU4OSIsIm5iZiI6IjE2MTI0NzI4NDQuNjQzNTk1IiwiZXhwIjoiMTYxMjQ3NjQ0NC41MjUxMzAiLCJzdWIiOiJleGFtcGxlQHVzZXIuY29tIiwic2NvcGVzIjpbImNsaWVudF9hcGkiXX0.DS2lcYzbzeocmPCL_kA-1HQgE7AQgenalgaHXJ3IAHuqmuZ7BL71x_F1qDxso2Fq1TxSOYKnCQE3Iw7g479vGjddpRJKm94nfEY6DTpGnTIUHogLS-ZoJv501br7lveD2tx-L9w5p4MUYlz2QeXHNIfHwZj9s03OVi9ghoKoypX7eO0y-NB5V5484Nh6hJs6RwgRRwHwdQ2GBFgR1qRpamDcBxBdJW-Go0YP9FqQF_mmDsbeTtvJsMGouaMPqx0hBfr0MkQaVSTY0YP34w86c8X-I3cbx-BnU7U6YMCDq-KDX3y0QO84LrQNgFZd7S5jWun90zWXuVBElch5uCZEBg'
```

Response from REST API should look like:

```json
{
    "id": "59e73485-e967-4fe0-b07e-a5bb1e36580d",
    "email": "example@user.com"
}
```

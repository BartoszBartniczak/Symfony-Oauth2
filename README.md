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

### Project stack

* PHP8
* Symfony 5
* Postgres 12

### How to run this project

1. Build the project
```bash
docker-compose build
```

2. Run the project
```bash
docker-compose up -d
```
>If you are using MacOs, you can start Mutagen for better development performance.
> ```bash
> mutagen project start
> ```

3. Generate RSA keys for JWT
```bash
docker-compose exec php bin/generate-keys
```

4. Update database schema
```bash
docker-compose exec php bin/console doctrine:schema:update --force
```

5. Apply database migrations
```bash
docker-compose exec php bin/console doctrine:migrations:migrate -n
```

6. Now you can create OAuth2 clients
```bash
docker-compose exec php bin/console trikoder:oauth2:create-client --scope client-api --grant-type password react-client-app
docker-compose exec php bin/console trikoder:oauth2:create-client --scope admin-api --grant-type password react-admin-app
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


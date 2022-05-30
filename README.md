Use the `.env.example` file to create a `.env` file.

To set up the project run `composer install` followed by `./vendor/bin/sail up -d`

After that you must run the migrations using the following command `./vendor/bin/sail migrate`

When all of that is done the API will be usable.

Public API routes:

- `api/client/create`
- `api/client/{id}/view`
- `api/client/{id}/update`
- `api/client/{id}/delete`
- `api/login`

Private API routes:

- `api/client/list`
- `api/notification/create`
- `api/notification/{id}`
- `api/notification/list`

For the private API routes, please use the login route to generate a token and use it as Bearer token. For logging in you may use the test user created in migrations with the following credentials:
```
Email: test@test.com
Password: test
```

#### Remember to only run tests after the migrations have been run!
To run tests use the following command: `./vendor/bin/sail test`
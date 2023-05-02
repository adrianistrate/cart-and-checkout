# Steps

## First step

```bash
make run
```

## Get inside the PHP container

```
make in-php
```

## While inside PHP container

```
symfony console doctrine:migrations:migrate
yarn install
yarn dev --watch
```

## Open up a new PHP container

#### Needed for sending out the emails

```
make in-php
symfony console messenger:consume --env=dev -vvv
```

## In browser

Go to: 
- client: https://localhost
- admin: https://localhost/admin

## Mailer

Open this in browser, to see emails: http://localhost:1080/


## Notes:

An admin should have a role `ROLE_ADMIN` and a client should have a role `ROLE_USER`.
If you want a quick admin user, run this SQL query:

```sql
INSERT INTO 
    `user` (`id`, `email`, `roles`, `password`, `credit`, `created_at`, `updated_at`)
VALUES
	(1, 'admin@store.com', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$WEq7AajgI0anb2przLsNsA$Dqrw+4U/o++RHS50sx+/Lj1qc1VmoJ88O7MVSQZU7dg', 0.00, '2023-05-02 06:22:07', '2023-05-02 06:22:07');
```

The password is `123456`.

# Flex-DB

This is the first Flex-DB engine system, a super simple yet powerful database engine. This is an filesystem-based database system
built in PHP to run as a standalone server. This database engine works by REST connections and by a token
shared between two applications.

Its configuration is very simple and fast, being able to release the use of the database for production in record time.

## Flex-DB main features

- Rest API support;
- API authorization though symetric tokens;
- Database based in JSON files;
- Select, where and pagination queries;
- Support to multiple collections;

## Installing

1. First, clone this repo in your server public directory.

2. Run `composer install`.

3. Create `storage` directory with permissions `775`.

4. Clone the environment file with `cp environment.json.example environment.json`.

Pretty simple, huh? Now, let's see how to setup the environment. Here's an environment file example:

```json
{
    "development": "true",
    "tokens": [
        {
            "token": "nyARrvQRTruam9XS",
            "label": "My application",
            "permissions": [
                "collection.*"
            ]
        }
    ]
}
```

- `development` defines if server is on development mode. If so, error messages will be echoed to responses.
- `tokens` an array of entities which can access the database.
- `tokens.token` the symetric key between the client and the server. You can put anything in there, just make sure to make it secure.
- `tokens.label` the entity label/description you want.
- `tokens.permissions` permissions for the entity's token. For this, permissions work by collection, or wildcard prefixes.
  - `collection.read` will grant access to query all contents in the database server.
  - `collection.*` will grant access to query and write/edit/delete contents in the database server.
  - `collection.users.*` will grant access to read/write contents in the `users` collection. This will also match columns which "users." is a prefix.
  - `collection.business.users.read` will grand query/read access to `business.users` collection.

# Create/Update Users

| Request     | Supported |
|-------------|-----------|
| Create User | ✅         |
| Update User | ✅         |

### Create User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;

$user = $connector->send(new CreateUser(new User(
    name: $timestamp.' - Test User',
    dbName: $timestamp,
    email: $timestamp.'-test@example.test',
    password: 'TESTPASSWORD',
)))->dto();
```

### Update User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;

$user->name .= ' - Updated';
$user->active = false;

$user = $connector->send(new UpdateUser($user))->dto();
```

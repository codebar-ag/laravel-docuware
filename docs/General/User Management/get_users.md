# Get Users

| Request              | Supported |
|----------------------|-----------|
| Get Users            | ✅         |
| Get Users by ID      | ✅         |
| Get Users of a Role  | ✅         |
| Get Users of a Group | ✅         |

### Get Users
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;

$users = $this->connector->send(new GetUsers())->dto();
```

### Get User By Id
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUserById;

$user = $this->connector->send(new GetUserById($userId))->dto();
```

### Get Users Of A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfARole;

$users = $this->connector->send(new GetUsersOfARole($roleId))->dto();
```

### Get Users Of A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfAGroup;

$users = $this->connector->send(new GetUsersOfAGroup($groupId))->dto();
```

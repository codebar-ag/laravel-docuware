# Get/Modify Roles

| Request                           | Supported |
|-----------------------------------|-----------|
| Get Roles                         | ✅         |
| Get All Roles for a Specific User | ✅         |
| Add User to a Role                | ✅         |
| Remove User from a Role           | ✅         |

### Get Roles
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetRoles;

$roles = $this->connector->send(new GetRoles())->dto();
```

### Get All Roles For A Specific User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetAllRolesForASpecificUser;

$roles = $connector->send(new GetAllRolesForASpecificUser($userId))->dto();
```

### Add User To A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\AddUserToARole;

$response = $connector->send(new AddUserToARole(
    userId: $userId,
    ids: [$roleId],
))->dto();
```

### Remove User From A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\RemoveUserFromARole;

$response = $connector->send(new RemoveUserFromARole(
    userId: $userId,
    ids: [$roleId],
))->dto();
```

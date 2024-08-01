# Get/Modify Groups

| Request                            | Supported |
|------------------------------------|-----------|
| Get Groups                         | ✅         |
| Get All Groups for a Specific User | ✅         |
| Add User to a Group                | ✅         |
| Remove User from a Group           | ✅         |

### Get Groups
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetGroups;

$groups = $connector->send(new GetGroups())->dto();
```

### Get All Groups For A Specific User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetAllGroupsForASpecificUser;

$groups = $connector->send(new GetAllGroupsForASpecificUser($userId))->dto();
```

### Add User To A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\AddUserToAGroup;

$response = $connector->send(new AddUserToAGroup(
    userId: $userId,
    ids: [$groupId],
))->dto();
```

### Remove User From A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\RemoveUserFromAGroup;

$response = $connector->send(new RemoveUserFromAGroup(
    userId: $userId,
    ids: [$groupId],
))->dto();
```

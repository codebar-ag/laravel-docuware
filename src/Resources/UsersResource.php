<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Users\GroupData;
use CodebarAg\DocuWare\Data\Users\RoleData;
use CodebarAg\DocuWare\Data\Users\UserData;
use CodebarAg\DocuWare\Data\Write\UserInput;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\AddUserToAGroup;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetAllGroupsForASpecificUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\RemoveUserFromAGroup;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\AddUserToARole;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetAllRolesForASpecificUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\RemoveUserFromARole;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUserById;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfAGroup;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfARole;
use Illuminate\Support\Collection;

/**
 * Organization users. Reached via `DocuWare::users()`.
 */
final class UsersResource extends Resource
{
    /**
     * @return Collection<int, UserData>
     */
    public function all(?string $name = null, ?bool $active = null): Collection
    {
        $response = $this->send(new GetUsers($name, $active));

        return $this->mapList($response, 'User', fn (array $user) => UserData::fromDocuWare($user));
    }

    public function find(string $userId): UserData
    {
        $response = $this->send(new GetUserById($userId));

        return UserData::fromDocuWare($response->json());
    }

    /**
     * @return Collection<int, UserData>
     */
    public function ofGroup(string $groupId): Collection
    {
        $response = $this->send(new GetUsersOfAGroup($groupId));

        return $this->mapList($response, 'User', fn (array $user) => UserData::fromDocuWare($user));
    }

    /**
     * @return Collection<int, UserData>
     */
    public function ofRole(string $roleId, ?bool $includeGroupUsers = null): Collection
    {
        $response = $this->send(new GetUsersOfARole($roleId, $includeGroupUsers));

        return $this->mapList($response, 'User', fn (array $user) => UserData::fromDocuWare($user));
    }

    /**
     * Create a new user.
     */
    public function create(UserInput $user): UserData
    {
        $response = $this->send(new CreateUser($user->toRequestDto()));

        return UserData::fromDocuWare($response->json());
    }

    /**
     * Update a user. Use the read-modify-write flow:
     * `$users->update($users->find($id)->copyWith(active: false))`.
     */
    public function update(UserData $user): UserData
    {
        $response = $this->send(new UpdateUser($user));

        return UserData::fromDocuWare($response->json());
    }

    /**
     * @param  array<int, string>  $groupIds
     */
    public function addToGroup(string $userId, array $groupIds): void
    {
        $this->send(new AddUserToAGroup($userId, $groupIds));
    }

    /**
     * @param  array<int, string>  $groupIds
     */
    public function removeFromGroup(string $userId, array $groupIds): void
    {
        $this->send(new RemoveUserFromAGroup($userId, $groupIds));
    }

    /**
     * @param  array<int, string>  $roleIds
     */
    public function addToRole(string $userId, array $roleIds): void
    {
        $this->send(new AddUserToARole($userId, $roleIds));
    }

    /**
     * @param  array<int, string>  $roleIds
     */
    public function removeFromRole(string $userId, array $roleIds): void
    {
        $this->send(new RemoveUserFromARole($userId, $roleIds));
    }

    /**
     * @return Collection<int, GroupData>
     */
    public function groupsOf(string $userId, ?string $name = null, ?bool $active = null): Collection
    {
        $response = $this->send(new GetAllGroupsForASpecificUser($userId, $name, $active));

        return $this->mapList($response, 'Item', fn (array $group) => GroupData::fromDocuWare($group));
    }

    /**
     * @return Collection<int, RoleData>
     */
    public function rolesOf(string $userId, ?string $name = null, ?bool $active = null, ?string $type = null): Collection
    {
        $response = $this->send(new GetAllRolesForASpecificUser($userId, $name, $active, $type));

        return $this->mapList($response, 'Item', fn (array $role) => RoleData::fromDocuWare($role));
    }
}

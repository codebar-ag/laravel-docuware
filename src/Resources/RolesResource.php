<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Users\RoleData;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetRoles;
use Illuminate\Support\Collection;

/**
 * Organization roles. Reached via `DocuWare::roles()`.
 */
final class RolesResource extends Resource
{
    /**
     * @return Collection<int, RoleData>
     */
    public function all(?string $name = null, ?bool $active = null, ?string $type = null): Collection
    {
        $response = $this->send(new GetRoles($name, $active, $type));

        return $this->mapList($response, 'Item', fn (array $role) => RoleData::fromDocuWare($role));
    }
}

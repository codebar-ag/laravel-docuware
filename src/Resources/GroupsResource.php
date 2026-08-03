<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Users\GroupData;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetGroups;
use Illuminate\Support\Collection;

/**
 * Organization groups. Reached via `DocuWare::groups()`.
 */
final class GroupsResource extends Resource
{
    /**
     * @return Collection<int, GroupData>
     */
    public function all(?string $name = null, ?bool $active = null): Collection
    {
        $response = $this->send(new GetGroups($name, $active));

        return $this->mapList($response, 'Item', fn (array $group) => GroupData::fromDocuWare($group));
    }
}

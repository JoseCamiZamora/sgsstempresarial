<?php
namespace App\Support;

final class EmployeePortalPendingItem
{
    public function __construct(
        public readonly string $category,
        public readonly string $signableId,
        public readonly string $label,
        public readonly ?string $subtitle,
        public readonly \DateTimeInterface $date,
        public readonly string $signRouteName,
        public readonly array $signRouteParams,
        public readonly ?string $badge = null,
    ) {
    }
}

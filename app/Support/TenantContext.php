<?php

declare(strict_types=1);

namespace App\Support;

class TenantContext
{
    public function __construct(
        private ?int $id = null,
        private ?string $slug = null,
    ) {
    }

    public function setTenant(int $id, string $slug): void
    {
        $this->id = $id;
        $this->slug = $slug;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function slug(): ?string
    {
        return $this->slug;
    }

    public function isResolved(): bool
    {
        return $this->id !== null;
    }
}

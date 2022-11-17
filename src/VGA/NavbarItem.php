<?php
namespace App\VGA;

class NavbarItem
{
    public string $title;

    public int $order;

    public ?string $route;

    /** @var NavbarItem[] */
    public array $children = [];

    public function __construct(array $details, ?string $route = null)
    {
        $this->title = $details['label'];
        $this->order = $details['order'];
        $this->route = $route;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @return NavbarItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(NavbarItem $child): NavbarItem
    {
        $this->children[] = $child;
        return $this;
    }

    public function isDropdown(): bool
    {
        return $this->route === null;
    }
}

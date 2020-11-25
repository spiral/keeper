<?php

declare(strict_types=1);

namespace Spiral\Keeper\Annotation\Sitemap;

trait ParentTrait
{
    public function hasRelativeParent(): bool
    {
        if ($this->parent && is_string($this->parent)) {
            $parent = trim($this->parent, ' .');
            return $parent && mb_strpos($parent, '.') === false;
        }
        return false;
    }

    public function hasAbsoluteParent(): bool
    {
        if ($this->parent && is_string($this->parent)) {
            $parent = trim($this->parent, ' .');
            return $parent && mb_strpos($parent, '.') !== false;
        }
        return false;
    }
}

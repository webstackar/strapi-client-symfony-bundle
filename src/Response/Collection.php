<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Response;

use Doctrine\Common\Collections\ArrayCollection;

class Collection extends ArrayCollection
{

    public function __construct(
        private readonly string $typeClass = Entry::class,
        private readonly array  $data = [],
        private readonly array  $meta = []
    ) {
        parent::__construct($this->transformElements());
    }

    public function getTotal(): int
    {
        return $this->meta['pagination']['total'] ?? $this->count();
    }

    public function getPageCount(): int
    {
        return $this->meta['pagination']['pageCount'] ?? 1;
    }

    /**
     * @return Entry[]
     */
    protected function transformElements(): array
    {
        return array_map(function ($el){
            return new $this->typeClass($el);
        }, $this->data);
    }
}
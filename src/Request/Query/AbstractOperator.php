<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Request\Query;

Abstract class AbstractOperator implements \Stringable
{
    public function __construct(protected array|string $params = []){}

    public function getName(): string
    {
        return lcfirst((new \ReflectionClass($this))->getShortName());
    }

    public function getParams(): array|string
    {
        return $this->params;
    }

    public function buildQuery() : string
    {
        return http_build_query([
            $this->getName() => $this->params
        ]);
    }

    public function __toString(): string
    {
        return $this->buildQuery();
    }
}
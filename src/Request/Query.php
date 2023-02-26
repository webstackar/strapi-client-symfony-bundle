<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Request;

use Webstackar\StrapiClientBundle\Request\Query\OperatorInterface;

class Query
{

    /**
     * @param OperatorInterface[] $operators
     */
    public function __construct(private array $operators = []){}

    public function add(OperatorInterface $operator): static
    {
        $this->operators[$operator->getName()] = $operator;
        return $this;
    }

    /**
     * @return OperatorInterface[]
     */
    public function getOperators(): array
    {
        return $this->operators;
    }

    public function getParams(): array
    {
        return array_map(function ($operator){
            return $operator->getParams();
        }, $this->getOperators());
    }

    public function toString(): string
    {
        $output = '';
        foreach ($this->getOperators() as $operator) {
            $output .= (string)$operator . '&';
        }
        return trim($output, '&');
    }
}
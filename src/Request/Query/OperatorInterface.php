<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Request\Query;

interface OperatorInterface
{
    public function getName() : string;
}
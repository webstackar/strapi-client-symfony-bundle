<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */
namespace Webstackar\StrapiClientBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class WebstackarStrapiClientBundle extends AbstractBundle
{

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void
    {
        $containerConfigurator->parameters()
            ->set('webstackar.strapi_client.api_url', 'http://localhost:1337/')
            ->set('webstackar.strapi_client.api_token', 'GIVE_ME_THE_REAL_TOKEN')
            ->set('webstackar.strapi_client.debug', false)
        ;
    }
}
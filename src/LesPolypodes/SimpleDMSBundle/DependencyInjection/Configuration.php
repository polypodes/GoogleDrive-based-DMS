<?php

namespace LesPolypodes\SimpleDMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode    = $treeBuilder->root('dms');
        $rootNode
            ->children()
                ->scalarNode('service_account_key_file')
                ->info('Service account key file. See https://code.google.com/apis/console')
                ->cannotBeEmpty()
                    ->defaultValue('%dms.service_account_key_file%')
                ->end()
                ->scalarNode('service_account_email')
                    ->info('Service account e-mail address. See https://code.google.com/apis/console')
                    ->cannotBeEmpty()
                    ->defaultValue('%dms.service_account_email')
                ->end()
            ->end();

        return $treeBuilder;

        return $treeBuilder;
    }
}

<?php
declare(strict_types=1);

/*
 * This file is part of the AL labs package
 *
 * (c) Arnaud Langlade
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Al\AppBundle\DependencyInjection\Compiler;

use Al\AppBundle\AppBundle;
use PhpCsFixer\Finder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ValidatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $validationFiles = [];
        foreach (Finder::create()->files()->in($this->getRootDirectory())->name('*.yml') as $file) {
            $validationFiles[] = $file->getPathname();
        }

        $container->getDefinition('validator.builder')->addMethodCall(
            'addYamlMappings',
            [$validationFiles]
        );
    }

    /**
     * @return string
     */
    private function getRootDirectory(): string
    {
        $bundleClass = new \ReflectionClass(AppBundle::class);
        $rootDirectory = sprintf(
            '%s/../Application/Employee/Resources/validation',
            dirname($bundleClass->getFileName())
        );

        return $rootDirectory;
    }
}

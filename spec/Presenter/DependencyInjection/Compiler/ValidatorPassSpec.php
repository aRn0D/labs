<?php

namespace spec\Al\Presenter\DependencyInjection\Compiler;

use Al\Presenter\AppBundle;
use Al\Presenter\DependencyInjection\Compiler\ValidatorPass;
use Symfony\Component\DependencyInjection\Definition;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidatorPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ValidatorPass::class);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    /**
     * @error: a spec must not acces to the pim
     */
    function it_load_validation_constraints(ContainerBuilder $container, Definition $validatorBuilder)
    {
        $bundleClass = new \ReflectionClass(AppBundle::class);
        $rootDirectory = dirname($bundleClass->getFileName());

        $validationFiles = [
            sprintf('%s/../Application/Resources/validation/FireEmployee.yml', $rootDirectory),
            sprintf('%s/../Application/Resources/validation/HireEmployee.yml', $rootDirectory),
            sprintf('%s/../Application/Resources/validation/PromoteEmployee.yml', $rootDirectory),
        ];

        $container->getDefinition('validator.builder')->willReturn($validatorBuilder);
        $validatorBuilder->addMethodCall('addYamlMappings', [$validationFiles])->shouldBeCalled();

        $this->process($container)->shouldReturn(null);
    }
}

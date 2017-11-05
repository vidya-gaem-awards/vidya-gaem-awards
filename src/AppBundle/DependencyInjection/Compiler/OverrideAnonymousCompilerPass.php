<?php
namespace AppBundle\DependencyInjection\Compiler;

use AppBundle\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideAnonymousCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('security.authentication.listener.anonymous');
        $definition->setClass(AnonymousAuthenticationListener::class);
    }
}

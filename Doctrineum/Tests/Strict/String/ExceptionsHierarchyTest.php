<?php
namespace Doctrineum\Tests\Strict\String;

use Doctrineum\Scalar\Enum;
use Granam\Exceptions\Tests\Tools\AbstractTestOfExceptionsHierarchy;

class ExceptionsHierarchyTest extends AbstractTestOfExceptionsHierarchy
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExternalRootNamespaces()
    {
        $externalRootReflection = new \ReflectionClass(Enum::class);

        return [
            $externalRootReflection->getNamespaceName()
        ];
    }

}
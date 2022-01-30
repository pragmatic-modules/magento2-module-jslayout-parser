<?php
declare(strict_types=1);

namespace Pragmatic\JsLayoutParser\Model;

use Pragmatic\JsLayoutParser\Api\ComponentInterface;
use Pragmatic\JsLayoutParser\Api\ComponentInterfaceFactory as ComponentFactory;

class JsLayoutParser
{
    /** @var ComponentFactory */
    private $componentFactory;

    public function __construct(
        ComponentFactory $componentFactory
    ) {
        $this->componentFactory = $componentFactory;
    }

    public function parse(array $jsLayout, string $rootComponent) : ComponentInterface
    {
        return $this->componentFactory->create([
            'data' => $jsLayout['components'][$rootComponent],
            'componentName' => $rootComponent
        ]);
    }
}

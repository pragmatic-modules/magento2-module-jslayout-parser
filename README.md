# JS Layout Parser

Lightweight Magento 2 module created to make working with `$jsLayout` in PHP less spaghetti, and more object-oriented.

### Installation

`composer require pragmatic-modules/js-layout-parser`

`bin/magento module:enable Pragmatic_JsLayoutParser`

`bin/magento setup:upgrade`

### Example usage with Magento's Checkout
File: `etc/frontend/di.xml`
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Magento\Checkout\Block\Onepage">
        <arguments>
            <argument name="layoutProcessors" xsi:type="array">
                <item name="example_processor" xsi:type="object">Pragmatic\Example\Block\Checkout\ExampleProcessor</item>
            </argument>
        </arguments>
    </type>
</config>

```

Processor:
```php
<?php
declare(strict_types=1);

namespace Pragmatic\Example\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Pragmatic\JsLayoutParser\Api\ComponentInterface;
use Pragmatic\JsLayoutParser\Api\ComponentInterfaceFactory as ComponentFactory;

class ExampleProcessor implements LayoutProcessorInterface
{
    /** @var ComponentFactory */
    private $componentFactory;

    public function __construct(
        ComponentFactory $componentFactory
    ) {
        $this->componentFactory = $componentFactory;
    }

    public function process($jsLayout)
    {
        /** @var ComponentInterface $component */
        $component = $this->componentFactory->create([
            'data' => $jsLayout['components']['checkout'],
            'componentName' => 'checkout'
        ]);

        if ($component->hasNestedChild('steps.shipping-step.shippingAddress')) {
            $shippingAddress = $component->getNestedChild('steps.shipping-step.shippingAddress');
            $shippingAddress->setComponent('Vendor_Module/js/view/shipping');
            $shippingAddress->setIsVisible(false);
        }
      
        $jsLayout['components']['checkout'] = $component->asArray();

        return $jsLayout;
    }
}
```

### Available Methods

```php
Pragmatic\JsLayoutParser\Api\ComponentInterface::class
    getName() : string;
    getParent() : ?ComponentInterface;
    setParent(ComponentInterface $component) : ComponentInterface;
    getChild(string $componentName): ComponentInterface;
    addChild(string $componentName, ComponentInterface $component): ComponentInterface;
    removeChild(string $componentName): ComponentInterface;
    hasChild(string $componentName): bool;
    hasNestedChild(string $path, string $childSeparator = '.'): bool;
    getNestedChild(string $path, string $childSeparator = '.') : ComponentInterface;
    moveNestedChild(string $sourcePath, string $destinationPath, string $childSeparator = '.') : ComponentInterface;
    hasChildren(): bool;
    getChildren(): array;
    isChildOf(ComponentInterface $component);
    setData(string $key, $value): ComponentInterface;
    getData(string $key);
    getComponent(): ?string;
    setComponent(string $component): ComponentInterface;
    getConfig(): array;
    setConfig(array $config): ComponentInterface;
    getDataScope(): ?string;
    setDataScope(string $dataScope): ComponentInterface;
    getDisplayArea(): ?string;
    setDisplayArea(string $displayArea): ComponentInterface;
    getLabel();
    setLabel($label): ComponentInterface;
    getProvider(): ?string;
    setProvider(string $provider): ComponentInterface;
    getSortOrder(): ?string;
    setSortOrder(string $sortOrder): ComponentInterface;
    getValidation(): array;
    setValidation(array $validation): ComponentInterface;
    getValue();
    setValue($value): ComponentInterface;
    getFilterBy(): ?string;
    setFilterBy(string $filterBy): ComponentInterface;
    isVisible(): bool;
    setIsVisible(bool $visible): ComponentInterface;
    isRequired(): bool;
    setIsRequired(bool $required): ComponentInterface;
    asArray(): array;
```

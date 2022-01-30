# JS Layout Parser

Lightweight Magento 2 module created to make working with `$jsLayout` in PHP less spaghetti, and more object-oriented.

## Installation

`composer require pragmatic-modules/js-layout-parser`

`bin/magento module:enable Pragmatic_JsLayoutParser`

`bin/magento setup:upgrade`

## Usage On Checkout

Add new layout processor by implementing `LayoutProcessorInterface`, and inject it into `layoutProcessors` array.

File: `etc/frontend/di.xml`

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Magento\Checkout\Block\Onepage">
        <arguments>
            <argument name="layoutProcessors" xsi:type="array">
                <item name="example_processor" xsi:type="object">Pragmatic\Example\Block\Checkout\ExampleProcessor
                </item>
            </argument>
        </arguments>
    </type>
</config>

```

```php
<?php
declare(strict_types=1);

namespace Pragmatic\Example\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Pragmatic\JsLayoutParser\Api\ComponentInterface;
use Pragmatic\JsLayoutParser\Model\JsLayoutParser;

class ExampleProcessor implements LayoutProcessorInterface
{
    /** @var JsLayoutParser */
    private $jsLayoutParser;

    public function __construct(JsLayoutParser $jsLayoutParser)
    {
        $this->jsLayoutParser = $jsLayoutParser;
    }

    public function process($jsLayout) : array
    {
        /** @var ComponentInterface $component */
        $component = $this->jsLayoutParser->parse($jsLayout, 'checkout');

        if ($shippingAddress = $component->getNestedChild('steps.shipping-step.shippingAddress')) {
            $shippingAddress->setComponent('Vendor_Module/js/view/shipping');
            $shippingAddress->setIsVisible(false);
        }
      
        $jsLayout['components']['checkout'] = $component->asArray();

        return $jsLayout;
    }
}
```

## Component Methods

### asArray ( )
#### Example:
#### jsLayout equivalent:

### getComponentName( )

Get component name in layout.

Returns string.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');
$checkout->getComponentName(); // returns 'checkout'
```

#### jsLayout equivalent:

In associative `$jsLayout` array components are stored as nested key-value pairs, where value are arrays with nested
children. It is not possible to determine parent's key within child scope. The same array might be used in multiple
places within jsLayout, so recursive searching is not an option.

In other words, you must know the name before retrieving component:

```php
$componentName = 'checkout';
$checkout = $jsLayout['components'][$componentName];
```

and once you get into child scope, there is no way to dynamically tell what's the parent key:

```php
$steps = $jsLayout['components']['checkout']['steps'];
// $steps tells you nothing about parent
```

The closest you can get to the parser behaviour is by doing:

```php
$checkout = [
    'componentName' => 'checkout', 
    'data' => $jsLayout['components']['checkout']
];
$checkout['componentName'] // returns 'checkout'
```

### getParent( )

Get parent component.

Returns `ComponentInterface` if parent exists.

Returns **NULL** if parent does not exist.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

$parent = $checkout->getParent(); // returns null

if($steps = $checkout->getChild('steps')) {
    $parent = $steps->getParent(); // returns $checkout object
}
```

#### jsLayout equivalent:

```php
$checkout = $jsLayout['components']['checkout'];
$parent = null;

if(isset($checkout['steps'])) {
    $steps = $checkout['steps'];
    $parent = $checkout;
}
```

### hasChild ( string $componentName )

Check if component has a child with a given name.

Returns boolean.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($checkout->hasChild('steps')) {
    // do something
}

if($checkout->hasChild('non-existing-child')) {
    // this won't execute
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['component']['checkout']['children']['steps'])) {
    // do something
}

if(isset($jsLayout['component']['checkout']['children']['non-existing-child'])) {
    // this won't execute
}
```

### getChild( string $componentName )

Get component child.

Returns `ComponentInterface` if child exists.

Returns **NULL** if child does not exist.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($child = $checkout->getChild('steps')) {
    // do something with child
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']['steps'])) {
    $child = $jsLayout['components']['checkout']['children']['steps'];
    // do something with child
}
```

### addChild( ComponentInterface $component )

Add new component as a child of the current component object.

This method throws `LocalizedException` if component with the same name is already a child of current component.

Returns self on success.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

/** @var \Pragmatic\JsLayoutParser\Api\ComponentInterfaceFactory */
$child = $this->componentFactory->create([
    'componentName' => 'example',
    'data' => [
        'component' => 'Magento_Ui/js/form/element/abstract',
        'label' => 'Example component',
        'provider' => 'checkoutProvider'
    ]   
]);

if(!$checkout->hasChild('example')) {
    $checkout->addChild($child);
}
```

#### jsLayout equivalent:

```php
if(!isset($jsLayout['components']['checkout']['children']['example'])) {
    $jsLayout['components']['checkout']['children']['example'] = [
        'component' => 'Magento_Ui/js/form/element/abstract',
        'label' => 'Example component',
        'provider' => 'checkoutProvider'
    ];
}
```

### removeChild ( string $componentName )

Remove child from the component.

This method throws `LocalizedException` if child does not exist within component.

This method has no return value.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($checkout->hasChild('steps')) {
    $checkout->removeChild('steps');
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']['steps'])) {
    unset($jsLayout['components']['checkout']['children']['steps']);
}
```

### hasNestedChild ( string $path, string $childSeparator = '.' )

Check if component has a nested child with a given path.

By default, children are separated by a dot. This behaviour can be adjusted by passing custom separator as a second
argument.

Returns boolean.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($checkout->hasNestedChild('steps.shipping-step.shippingAddress')) {
    // do something
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress'])
) {
    // do something
}
```

### getNestedChild ( string $componentName, string $childSeparator = '.' )

Get component nested child.

By default, children are separated by a dot. This behaviour can be adjusted by passing custom separator as a second
argument.

Returns `ComponentInterface` if nested child exists.

Returns **NULL** if nested child does not exist.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($shippingAddress = $checkout->getNestedChild('steps.shipping-step.shippingAddress')) {
    // do something with $shippingAddress
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress']
)) {
    $shippingAddress = $jsLayout['components']['checkout']['children']
        ['steps']['children']
        ['shipping-step']['children']
        ['shippingAddress'];
    // do something with $shippingAddress
}
```

### moveNestedChild ( string $sourcePath, string $destinationPath, string $childSeparator = '.' )

Move nested child from source to destination.

By default, children are separated by a dot. This behaviour can be adjusted by passing custom separator as a third
argument.

This method throws `LocalizedException` if source or destination does not exist.

This method has no return value.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($checkout->hasNestedChild('steps.shipping-step.shippingAddress') && 
   $checkout->hasChild('steps')
) {
    $checkout->moveNestedChild('steps.shipping-step.shippingAddress', 'steps');
}

$checkout->hasNestedChild('steps.shipping-step.shippingAddress') // false
$checkout->hasNestedChild('steps.shippingAddress') // true
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress']
) && isset($jsLayout['components']['checkout']['children']['steps'])
) {
    $steps = &$jsLayout['components']['checkout']['children']
    ['steps']['children'];
    $shippingAddress = $steps['shipping-step']['children']['shippingAddress'];
    unset($steps['shipping-step']['children']['shippingAddress']);
    $steps['shippingAddress'] = $shippingAddress;
}

isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress']
) // false

isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shippingAddress']
) // true
```

### hasChildren ( )

Check if component has children.

Returns true if at least one child exists, returns false otherwise.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

$checkout->hasChildren() // returns true
```

#### jsLayout equivalent:

```php
(isset($jsLayout['components']['checkout']['children']) && 
count($jsLayout['components']['checkout']['children']) > 0) // returns true
```

### getChildren ( )

Get component children.

Returns array of components.

Returns empty array if component has no children.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

$checkout->getChildren(); // returns array with 'steps' component
```

#### jsLayout equivalent:

```php
$jsLayout['components']['checkout']['children'] ?? [] // returns array with 'steps' component
```

### isChildOf ( ComponentInterface $component )

Check if component is child of given component.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

$steps = $checkout->getChild('steps');
$steps->isChildOf($checkout) // returns true
```

#### jsLayout equivalent:

In associative `$jsLayout` array components are stored as nested key-value pairs, where value are arrays with nested
children. It is not possible to determine parent within child scope.

### getComponent ( )

Get UI Component of given component object.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($shippingAddress = $checkout->getNestedChild('steps.shipping-step.shippingAddress')) {
    $shippingAddress->getComponent() // returns 'Magento_Checkout/js/view/shipping'
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress']
)) {
    $shippingAddress = $jsLayout['components']['checkout']['children']
        ['steps']['children']
        ['shipping-step']['children']
        ['shippingAddress'];
    $shippingAddress['component'] // 'Magento_Checkout/js/view/shipping'
}
```

### setComponent ( string $component )

Set UI Component of given component object.

#### Example:

```php
$checkout = $this->jsLayoutParser->parse($jsLayout, 'checkout');

if($shippingAddress = $checkout->getNestedChild('steps.shipping-step.shippingAddress')) {
    $shippingAddress->setComponent('Vendor_Module/js/view/shipping')
}
```

#### jsLayout equivalent:

```php
if(isset($jsLayout['components']['checkout']['children']
    ['steps']['children']
    ['shipping-step']['children']
    ['shippingAddress']
)) {
    $shippingAddress = &$jsLayout['components']['checkout']['children']
        ['steps']['children']
        ['shipping-step']['children']
        ['shippingAddress'];
    $shippingAddress['component'] = 'Vendor_Module/js/view/shipping';
}
```

### getConfig ( )
#### Example:
#### jsLayout equivalent:

### setConfig ( array $config )
#### Example:
#### jsLayout equivalent:

### getDataScope ( )
#### Example:
#### jsLayout equivalent:

### setDataScope ( string $dataScope )
#### Example:
#### jsLayout equivalent:

### getDisplayArea ( )
#### Example:
#### jsLayout equivalent:

### setDisplayArea ( string $displayArea )
#### Example:
#### jsLayout equivalent:

### getLabel ( )
#### Example:
#### jsLayout equivalent:

### setLabel ( string $label )
#### Example:
#### jsLayout equivalent:

### getProvider ( )
#### Example:
#### jsLayout equivalent:

### setProvider ( string $provider )
#### Example:
#### jsLayout equivalent:

### getSortOrder ( )
#### Example:
#### jsLayout equivalent:

### setSortOrder ( string $sortOrder )
#### Example:
#### jsLayout equivalent:

### getValidation ( )
#### Example:
#### jsLayout equivalent:

### setValidation ( array $validation )
#### Example:
#### jsLayout equivalent:

### getFilterBy ( )
#### Example:
#### jsLayout equivalent:

### setFilterBy ( ?array $filterBy )
#### Example:
#### jsLayout equivalent:

### isVisible ( )
#### Example:
#### jsLayout equivalent:

### setIsVisible ( bool $visible )
#### Example:
#### jsLayout equivalent:

### isRequired ( )
#### Example:
#### jsLayout equivalent:

### setIsRequired ( bool $required )
#### Example:
#### jsLayout equivalent:

<?php
declare(strict_types=1);

namespace Pragmatic\JsLayoutParser\Api;

use Magento\Framework\Exception\LocalizedException;

interface ComponentInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return ComponentInterface|null
     */
    public function getParent() : ?ComponentInterface;

    /**
     * @param ComponentInterface $component
     * @return ComponentInterface
     */
    public function setParent(ComponentInterface $component) : ComponentInterface;

    /**
     * @param string $componentName
     * @return bool
     */
    public function hasChild(string $componentName): bool;

    /**
     * @param string $componentName
     * @return ComponentInterface
     * @throws LocalizedException
     */
    public function getChild(string $componentName): ComponentInterface;

    /**
     * @param string $componentName
     * @param ComponentInterface $component
     * @return ComponentInterface
     */
    public function addChild(string $componentName, ComponentInterface $component) : ComponentInterface;

    /**
     * @param string $componentName
     * @return void
     */
    public function removeChild(string $componentName): void;

    /**
     * @param string $path
     * @param string $childSeparator
     * @return bool
     */
    public function hasNestedChild(string $path, string $childSeparator = '.'): bool;

    /**
     * @param string $path
     * @param string $childSeparator
     * @return ComponentInterface
     * @throws LocalizedException
     */
    public function getNestedChild(string $path, string $childSeparator = '.') : ComponentInterface;

    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @param string $childSeparator
     * @return ComponentInterface
     */
    public function moveNestedChild(string $sourcePath, string $destinationPath, string $childSeparator = '.') : ComponentInterface;

    /**
     * @param string $path
     * @param string $childSeparator
     * @return ComponentInterface
     */
    public function removeNestedChild(string $path, string $childSeparator = '.') : ComponentInterface;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @return ComponentInterface[]
     */
    public function getChildren(): array;

    /**
     * @param ComponentInterface $component
     * @return mixed
     */
    public function isChildOf(ComponentInterface $component);

    /**
     * @param string $key
     * @param $value
     * @return ComponentInterface
     */
    public function setData(string $key, $value): ComponentInterface;

    /**
     * @param string $key
     * @return mixed
     */
    public function getData(string $key);

    /**
     * @return string|null
     */
    public function getComponent(): ?string;

    /**
     * @param string $component
     * @return ComponentInterface
     */
    public function setComponent(string $component): ComponentInterface;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param array $config
     * @return ComponentInterface
     */
    public function setConfig(array $config): ComponentInterface;

    /**
     * @return string|null
     */
    public function getDataScope(): ?string;

    /**
     * @param string $dataScope
     * @return ComponentInterface
     */
    public function setDataScope(string $dataScope): ComponentInterface;

    /**
     * @return string|null
     */
    public function getDisplayArea(): ?string;

    /**
     * @param string $displayArea
     * @return ComponentInterface
     */
    public function setDisplayArea(string $displayArea): ComponentInterface;

    /**
     * @return mixed
     */
    public function getLabel();

    /**
     * @param $label
     * @return ComponentInterface
     */
    public function setLabel($label): ComponentInterface;

    /**
     * @return string|null
     */
    public function getProvider(): ?string;

    /**
     * @param string $provider
     * @return ComponentInterface
     */
    public function setProvider(string $provider): ComponentInterface;

    /**
     * @return string|null
     */
    public function getSortOrder(): ?string;

    /**
     * @param string $sortOrder
     * @return ComponentInterface
     */
    public function setSortOrder(string $sortOrder): ComponentInterface;

    /**
     * @return array
     */
    public function getValidation(): array;

    /**
     * @param array $validation
     * @return ComponentInterface
     */
    public function setValidation(array $validation): ComponentInterface;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param $value
     * @return ComponentInterface
     */
    public function setValue($value): ComponentInterface;

    /**
     * @return string|null
     */
    public function getFilterBy(): ?string;

    /**
     * @param string $filterBy
     * @return ComponentInterface
     */
    public function setFilterBy(string $filterBy): ComponentInterface;

    /**
     * @return bool
     */
    public function isVisible(): bool;

    /**
     * @param bool $visible
     * @return ComponentInterface
     */
    public function setIsVisible(bool $visible): ComponentInterface;

    /**
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * @param bool $required
     * @return ComponentInterface
     */
    public function setIsRequired(bool $required): ComponentInterface;

    /**
     * @return array
     */
    public function asArray(): array;
}

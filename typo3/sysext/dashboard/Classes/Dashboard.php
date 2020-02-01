<?php
declare(strict_types = 1);
namespace TYPO3\CMS\Dashboard;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\Interfaces\WidgetInterface;

class Dashboard
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $widgetConfig;

    /**
     * @var WidgetRegistry
     */
    protected $widgetRegistry;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var WidgetInterface[]
     */
    protected $widgets = [];

    public function __construct(
        string $identifier,
        string $title,
        array $widgetConfig,
        WidgetRegistry $widgetRegistry,
        ContainerInterface $container
    ) {
        $this->identifier = $identifier;
        $this->title = $title;
        $this->widgetConfig = $widgetConfig;
        $this->widgetRegistry = $widgetRegistry;
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getLanguageService()->sl($this->title) ?: $this->title;
    }

    /**
     * @return array
     */
    public function getWidgetConfig(): array
    {
        return $this->widgetConfig;
    }

    /**
     * @return WidgetInterface[]
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * This will return a list of all widgets of the current dashboard object. It will only include available
     * widgets and will add the initialised object of the widget itself
     *
     * @return array
     */
    public function initializeWidgets(): void
    {
        $availableWidgets = $this->widgetRegistry->getAvailableWidgets();
        foreach ($this->widgetConfig as $hash => $widgetConfig) {
            if (array_key_exists($widgetConfig['identifier'], $availableWidgets)) {
                $widgetObject = $this->container->get($availableWidgets[$widgetConfig['identifier']]);
                if ($widgetObject instanceof WidgetInterface) {
                    $this->widgets[$hash] = $widgetObject;
                }
            }
        }
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}

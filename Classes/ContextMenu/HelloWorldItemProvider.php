<?php
namespace T3docs\Examples\ContextMenu;

/**
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

use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;

/**
 * Item provider adding Hello World item
 */
class HelloWorldItemProvider extends AbstractProvider
{
    /**
     * This array contains configuration for items you want to add
     * @var array
     */
    protected $itemsConfiguration = [
        'hello' => [
            'type' => 'item',
            'label' => 'Hello World', // you can use "LLL:" syntax here
            'iconIdentifier' => 'actions-lightbulb-on',
            'callbackAction' => 'helloWorld' //name of the function in the JS file
        ]
    ];

    /**
     * Checks if this provider may be called to provide the list of context menu items for given table.
     *
     * @return bool
     */
    public function canHandle(): bool
    {
        // Current table is: $this->table
        // Current UID is: $this->identifier
//        return $this->table === 'pages';
        return true;
    }

    /**
     * Returns the provider priority which is used for determining the order in which providers are processing items
     * to the result array. Highest priority means provider is evaluated first.
     *
     * This item provider should be called after PageProvider which has priority 100.
     *
     * BEWARE: Returned priority should logically not clash with another provider.
     *         Please check @see \TYPO3\CMS\Backend\ContextMenu\ContextMenu::getAvailableProviders() if needed.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return 55;
    }

    /**
     * Registers the additional JavaScript RequireJS callback-module which will allow to display a notification
     * whenever the user tries to click on the "Hello World" item.
     * The method is called from AbstractProvider::prepareItems() for each context menu item.
     *
     * @param string $itemName
     * @return array
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        return [
            // BEWARE!!! RequireJS MODULES MUST ALWAYS START WITH "TYPO3/CMS/" (and no "Vendor" segment here)
            'data-callback-module' => 'TYPO3/CMS/Examples/ContextMenuActions',
            // Here you can also add any other useful "data-" attribute you'd like to use in your JavaScript (e.g. localized messages)
        ];
    }

    /**
     * This method adds custom item to list of items generated by item providers with higher priority value (PageProvider)
     * You could also modify existing items here.
     * The new item is added after the 'info' item.
     *
     * @param array $items
     * @return array
     */
    public function addItems(array $items): array
    {
        $this->initDisabledItems();
        // renders an item based on the configuration from $this->itemsConfiguration
        $localItems = $this->prepareItems($this->itemsConfiguration);

        if (isset($items['info'])) {
            //finds a position of the item after which 'hello' item should be added
            $position = array_search('info', array_keys($items), true);

            //slices array into two parts
            $beginning = array_slice($items, 0, $position+1, true);
            $end = array_slice($items, $position, null, true);

            // adds custom item in the correct position
            $items = $beginning + $localItems + $end;
        } else {
            $items = $items + $localItems;
        }
        //passes array of items to the next item provider
        return $items;
    }

    /**
     * This method is called for each item this provider adds and checks if given item can be added
     *
     * @param string $itemName
     * @param string $type
     * @return bool
     */
    protected function canRender(string $itemName, string $type): bool
    {
        // checking if item is disabled through TSConfig
        if (in_array($itemName, $this->disabledItems, true)) {
            return false;
        }
        $canRender = false;
        switch ($itemName) {
            case 'hello':
                $canRender = $this->canSayHello();
                break;
        }
        return $canRender;
    }

    /**
     * Helper method implementing e.g. access check for certain item
     *
     * @return bool
     */
    protected function canSayHello(): bool
    {
         //usually here you can find more sophisticated condition. See e.g. PageProvider::canBeEdited()
         return true;
    }
}

<?php
/**
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2008-2017 AddThis, LLC                                     |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 2 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program; if not, write to the Free Software              |
 * | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
 * +--------------------------------------------------------------------------+
 */

require_once 'AddThisFeature.php';
require_once 'AddThisSharingButtonsFloatingTool.php';
require_once 'AddThisSharingButtonsInlineTool.php';
require_once 'AddThisSharingButtonsMobileToolbarTool.php';

if (!class_exists('AddThisSharingButtonsFeature')) {
    /**
     * Class for adding AddThis sharing buttonst tools to WordPress
     *
     * @category   SharingButtons
     * @package    AddThisWordPress
     * @subpackage Features
     * @author     AddThis <help@addthis.com>
     * @license    GNU General Public License, version 2
     * @link       http://addthis.com AddThis website
     */
    class AddThisSharingButtonsFeature extends AddThisFeature
    {
        protected $settingsVariableName = 'addthis_sharing_buttons_settings';
        protected $settingsPageId = 'addthis_sharing_buttons';
        protected $name = 'Share Buttons';
        protected $SharingButtonsFloatingToolObject = null;
        protected $SharingButtonsInlineToolObject = null;
        protected $SharingButtonsMobileToolbarToolObject = null;
        protected $filterPriority = 1;
        protected $filterNamePrefix = 'addthis_sharing_buttons_';
        protected $enableAboveContent = true;
        protected $enableBelowContent = true;

        // a list of all settings fields used for this feature that aren't tool
        // specific
        protected $settingsFields = array(
            'startUpgradeAt',
        );

        public $globalLayersJsonField = 'addthis_layers_share_json';
        public $globalEnabledField = 'sharing_buttons_feature_enabled';

        // require the files with the tool and widget classes at the top of this
        // file for each tool
        protected $tools = array(
            'SharingButtonsFloating',
            'SharingButtonsInline',
            'SharingButtonsMobileToolbar',
        );

        public $contentFiltersEnabled = true;

        /**
         * Builds the class used for sharing buttons above and below content on
         * pages, posts, categories, archives and the homepage
         *
         * @param string $location Is this for a sharing button above or below
         * content/excerpts?
         * @param array  $track    Optional. Used by reference. If the
         * filter changes the value in any way the filter's name will be pushed
         *
         * @return string a class
         */
        public function getClassForTypeAndLocation(
            $location = 'above',
            &$track = false
        ) {
            $toolClass = $this->getDefaultClassForTypeAndLocation($location);

            if ($location == 'above') {
                $filterName = $this->filterNamePrefix . 'above_tool';
            } else {
                $filterName = $this->filterNamePrefix . 'below_tool';
            }

            $toolClass = $this->applyToolClassFilters($toolClass, $location, $track);
            return $toolClass;
        }

        /**
         * Builds HTML for teling AddThis what URL to share for inline layers
         * buttons
         *
         * @param array $track Optional. Used by reference. If the
         * filter changes the value in any way the filter's name will be pushed
         *
         * @return string HTML attributes for telling AddThis what URL to share
         */
        public function getInlineLayersAttributes(&$track = false)
        {
            $dataUrlTemplate = 'data-url="%1$s"';
            $dataTitleTemplate = 'data-title="%1$s"';

            $attrs = array();
            $url = $this->getShareUrl($track);
            if (!empty($url)) {
                $attrs[] = sprintf($dataUrlTemplate, $url);
            }

            $title = $this->getShareTitle($track);
            if (!empty($title)) {
                $attrs[] = sprintf($dataTitleTemplate, $title);
            }

            $attrString = implode(' ', $attrs);
            return $attrString;
        }

        /**
         * This must be public as it's used in a callback for register_setting,
         * which is essentially a filter
         *
         * This takes form input for a settings variable, manipulates it, and
         * returns the variables that should be saved to the database.
         *
         * This version of the function overrides the one in AddThisFeature and
         * works with multiple versions of the same tool. Eventually this would
         * replace AddThisFeature::sanitizeSettings
         *
         * @param array $input An associative array of values input for this
         * feature's settings
         *
         * @return array A cleaned up associative array of settings specific to
         *               this feature.
         */
        public function sanitizeSettings($input)
        {
            $output = array();

            foreach ($this->settingsFields as $field) {
                if (!empty($input[$field])) {
                    $output[$field] = sanitize_text_field($input[$field]);
                }
            }

            if (is_array($input)) {
                foreach ($input as $key => $toolSettings) {
                    // determine which tool it is, and run it through the appropriate tool object's sanitizeSettings

                    if (isset($toolSettings['id'])) {
                        //if shfs - do special stuff to break out sharing sidebar and mobile sharing toolbar
                        if ($toolSettings['id'] === 'shfs') {
                            $toolObject = new AddThisSharingButtonsFloatingTool();
                        } elseif ($toolSettings['id'] === 'shin') {
                            $toolObject = new AddThisSharingButtonsInlineTool();
                        } elseif ($toolSettings['id'] === 'smlmo') {
                            $toolObject = new AddThisSharingButtonsMobileToolbarTool();
                        }

                        $toolOutput = $toolObject->sanitizeSettings($toolSettings);
                        $output[$toolOutput['widgetId']] = $toolOutput;
                    } elseif ($key === 'startUpgradeAt') {
                        $output['startUpgradeAt'] = $toolSettings['startUpgradeAt'];
                    }
                }
            }

            return $output;
        }

        /**
         * Returns tool specific settings for the JavaScript variable for each
         * tool in this feature set
         *
         * @return array an array of associative arrays
         */
        public function getAddThisLayersTools()
        {
            $allToolLayers = array();

            $configs = $this->getConfigs();
            if (is_array($configs)) {
                foreach ($configs as $toolSettings) {
                    if (!empty($toolSettings['id'])) {
                        if ($toolSettings['id'] === 'shfs') {
                            $toolObject = new AddThisSharingButtonsFloatingTool();
                        } elseif ($toolSettings['id'] === 'shin') {
                            $toolObject = new AddThisSharingButtonsInlineTool();
                        } elseif ($toolSettings['id'] === 'smlmo') {
                            $toolObject = new AddThisSharingButtonsMobileToolbarTool();
                        }

                        $toolLayers = $toolObject->getAddThisLayers($toolSettings);
                        if (!empty($toolLayers)) {
                            $allToolLayers[] = $toolLayers;
                        }
                    }
                }
            }

            return $allToolLayers;
        }

        /**
         * Upgrade from Smart Layers by AddThis 1.*.* to
         * Smart Layers by AddThis 2.0.0
         *
         * @return null
         */
        protected function upgradeIterative1()
        {
            $activated = get_option('smart_layer_activated');
            if (empty($activated)) {
                return null;
            }

            $advancedMode = get_option('smart_layer_settings_advanced');
            if (!empty($advancedMode)) {
                return null;
            }

            $jsonString = get_option('smart_layer_settings');
            $jsonString = preg_replace('/\'/', '"', $jsonString);
            $jsonDecoded = json_decode($jsonString, true);

            $followServices = array();
            if (!empty($jsonDecoded['follow']) &&
                !empty($jsonDecoded['follow']['services'])
            ) {
                // prep mobile toolbar folllow settings
                $oldServices = $jsonDecoded['follow']['services'];
                $followServices = AddThisFollowButtonsFeature::upgradeIterative2SmartLayersServices($oldServices);
            }

            $sharingSidebarConfigs = array();
            $mobileToolbarConfigs = array();
            if (isset($jsonDecoded['share'])) {
                // prep sharing sidebar settings & mobile toolbar settings
                $sharingSidebarConfigs['enabled'] = true;
                $mobileToolbarConfigs['enabled'] = true;

                if (isset($jsonDecoded['share']['position'])) {
                    $sharingSidebarConfigs['position'] = $jsonDecoded['share']['position'];
                }

                if (isset($jsonDecoded['share']['numPreferredServices'])) {
                    $sharingSidebarConfigs['numPreferredServices'] = (int)$jsonDecoded['share']['numPreferredServices'];
                }

                if (!empty($followServices)) {
                    // include follow services for mobile
                    $mobileToolbarConfigs['follow'] = 'on';
                    $mobileToolbarConfigs['followServices'] = $followServices;
                } else {
                    $mobileToolbarConfigs['follow'] = 'off';
                }

                if (isset($jsonDecoded['theme'])) {
                    $sharingSidebarConfigs['theme'] = $jsonDecoded['theme'];
                    $mobileToolbarConfigs['buttonBarTheme'] = $jsonDecoded['theme'];
                }
            }

            $this->configs['smlsh'] = $sharingSidebarConfigs;
            $this->configs['smlmo'] = $mobileToolbarConfigs;
        }

        /**
         * Upgrade from Smart Layers by AddThis 2.0.0 to 3.0.0
         * Upgrade from Website Tools by AddThis 1.1.2 to 2.0.0
         *
         * @return null
         */
        protected function upgradeIterative2()
        {
            $customShareWidgets = self::upgradeIterative2ReformatWidgets(
                'addthis_custom_sharing_widget',
                'addthis_custom_sharing'
            );

            $jumboShareWidgets = self::upgradeIterative2ReformatWidgets(
                'addthis_jumbo_share_widget',
                'addthis_jumbo_share'
            );

            $nativeShareWidgets = self::upgradeIterative2ReformatWidgets(
                'addthis_native_toolbox_widget',
                'addthis_native_toolbox'
            );

            $responsiveShareWidgets = self::upgradeIterative2ReformatWidgets(
                'addthis_responsive_sharing_widget',
                'addthis_responsive_sharing'
            );

            $squareShareWidgets = self::upgradeIterative2ReformatWidgets(
                'addthis_sharing_buttons_widget',
                'addthis_sharing_toolbox'
            );

            $newWidgets = array_merge(
                $customShareWidgets,
                $jumboShareWidgets,
                $nativeShareWidgets,
                $responsiveShareWidgets,
                $squareShareWidgets
            );

            $widgetIdMapping = self::upgradeIterative2SaveWidgets($newWidgets);
            AddThisFollowButtonsFeature::upgradeIterative1MigrateSidebarWidgetIds($widgetIdMapping);
        }

        /**
         * Reformats widgets settings from where the CSS class for the AddThis
         * tool is hard coded per widget PHP class, to one widget PHP class
         * which stores the proper CSS class as an instance variable for that
         * widget
         *
         * @param array $oldWidgetName old settings for widgets
         * @param array $class         the CSS class to use for all the old
         * widgets passed
         *
         * @return array associated array of reformatted widgets, keys are used
         * for migrating widgets
         */
        public static function upgradeIterative2ReformatWidgets($oldWidgetName, $class)
        {
            $oldWidgets = get_option('widget_' . $oldWidgetName);
            $newWidgets = array();

            if (!is_array($oldWidgets) || empty($oldWidgets)) {
                return array();
            }

            foreach ($oldWidgets as $key => $widget) {
                if ($key == '_multiwidget') {
                    continue;
                }

                $oldWidgetKey = $oldWidgetName . '-' . $key;
                $newWidgets[$oldWidgetKey] = array();
                $newWidgets[$oldWidgetKey]['title'] = $widget['title'];
                $newWidgets[$oldWidgetKey]['class'] = $class;
            }

            return $newWidgets;
        }


        /**
         * Saves new widgets by appending to existing, returns an array mapping
         * old widgets IDs to new ones
         *
         * @param array $inputWidgets new settings for widgets
         *
         * @return array associated array of old widgets IDs as keys and new
         * widget IDs as values
         */
        public static function upgradeIterative2SaveWidgets($inputWidgets)
        {
            if (empty($inputWidgets)) {
                return;
            }

            if (isset($inputWidgets['_multiwidget'])) {
                unset($inputWidgets['_multiwidget']);
            }

            $widgetIdMapping = array();
            $newWidgetName = 'addthis_tool_by_class_name_widget';
            $outputWidgets = get_option('widget_' . $newWidgetName);

            if (is_array($outputWidgets) && !empty($outputWidgets)) {
                if (!isset($outputWidgets['_multiwidget'])) {
                    unset($outputWidgets['_multiwidget']);
                }

                $widgetIdNextNumber = max(array_keys($outputWidgets)) + 1;
            } else {
                $widgetIdNextNumber = 0;
                $outputWidgets = array();
            }

            foreach ($inputWidgets as $key => $widget) {
                $newWidgetId = $newWidgetName . '-' . $widgetIdNextNumber;
                $oldWidgetId = $key;
                $widgetIdMapping[$oldWidgetId] = $newWidgetId;
                $outputWidgets[$widgetIdNextNumber] = $widget;

                $widgetIdNextNumber = $widgetIdNextNumber + 1;
            }
            $outputWidgets['_multiwidget'] = 1;

            update_option('widget_addthis_tool_by_class_name_widget', $outputWidgets);
            return $widgetIdMapping;
        }

        /**
         * Upgrade from Smart Layers by AddThis 2.0.0 to 3.0.0
         *
         * @return null
         */
        protected function upgradeIterative3()
        {
            $newConfigs = array();
            $oldConfigs = $this->getConfigs();
            if (!empty($oldConfigs['msd'])) {
                $toolSettings = array(
                    'enabled'               => $oldConfigs['msd']['enabled'],
                    'counts'                => (empty($oldConfigs['msd']['counts']) ? 'none' : 'one'),
                    'numPreferredServices'  => $oldConfigs['msd']['numPreferredServices'],
                    'mobilePosition'        => $oldConfigs['msd']['position'],
                    'services'              => $oldConfigs['msd']['services'],
                    'auto_personalization'  => (empty($oldConfigs['msd']['services']) ? true : false),
                    'style'                 => 'modern',
                    'theme'                 => 'transparent',
                    'mobileButtonSize'      => 'large',
                    'id'                    => 'shfs',
                    'desktopPosition'       => 'hide',
                    'toolName'              => 'Mobile Sharing Toolbar',
                    'widgetId'              => 'msd',
                    'templates'             => array(
                        'home',
                        'posts',
                        'pages',
                        'archives',
                        'categories',
                    ),
                );
                $newConfigs[$toolSettings['widgetId']] = $toolSettings;
            }

            if (!empty($oldConfigs['smlsh'])) {
                $toolSettings = array(
                    'enabled'               => $oldConfigs['smlsh']['enabled'],
                    'counts'                => (empty($oldConfigs['smlsh']['counts']) ? 'none' : 'one'),
                    'numPreferredServices'  => $oldConfigs['smlsh']['numPreferredServices'],
                    'desktopPosition'       => $oldConfigs['smlsh']['position'],
                    'services'              => $oldConfigs['smlsh']['services'],
                    'auto_personalization'  => (empty($oldConfigs['smlsh']['services']) ? true : false),
                    'style'                 => 'modern',
                    'theme'                 => 'transparent',
                    'id'                    => 'shfs',
                    'mobilePosition'        => 'hide',
                    'toolName'              => 'Sidebar',
                    'widgetId'              => 'smlsh',
                    'templates'             => array(
                        'home',
                        'posts',
                        'pages',
                        'archives',
                        'categories',
                    ),
                );
                $newConfigs[$toolSettings['widgetId']] = $toolSettings;
            }

            if (!empty($oldConfigs['tbx'])) {
                $toolSettings = array(
                    'enabled'               => $oldConfigs['tbx']['enabled'],
                    'counts'                => (empty($oldConfigs['tbx']['counts']) ? 'none' : 'one'),
                    'numPreferredServices'  => $oldConfigs['tbx']['numPreferredServices'],
                    'services'              => $oldConfigs['tbx']['services'],
                    'auto_personalization'  => (empty($oldConfigs['tbx']['services']) ? true : false),
                    'elements'              => $oldConfigs['tbx']['elements'],
                    'style'                 => 'fixed',
                    'id'                    => 'shin',
                    'toolName'              => 'Share Buttons',
                    'widgetId'              => 'tbx',
                );

                switch ($oldConfigs['tbx']['size']) {
                    case 'small':
                        $toolSettings['size'] = '16px';
                        break;
                    case 'medium':
                        $toolSettings['size'] = '20px';
                        break;
                    default:
                        $toolSettings['size'] = '32px';
                }

                $newConfigs[$toolSettings['widgetId']] = $toolSettings;
            }

            if (!empty($oldConfigs['scopl'])) {
                $toolSettings = array(
                    'enabled'               => $oldConfigs['scopl']['enabled'],
                    'auto_personalization'  => (empty($oldConfigs['scopl']['services']) ? true : false),
                    'originalServices'      => $oldConfigs['scopl']['services'],
                    'elements'              => $oldConfigs['scopl']['elements'],
                    'style'                 => 'original',
                    'id'                    => 'shin',
                    'toolName'              => 'Original Share Buttons',
                    'widgetId'              => 'scopl',
                );
                $newConfigs[$toolSettings['widgetId']] = $toolSettings;
            }

            if (!empty($oldConfigs['smlmo'])) {
                $oldConfigs['smlmo']['widgetId'] = 'smlmo';
                $oldConfigs['smlmo']['id'] = 'smlmo';
                $oldConfigs['smlmo']['toolName'] = 'Mobile Toolbar';
                $oldConfigs['smlmo']['templates'] = array(
                    'home',
                    'posts',
                    'pages',
                    'archives',
                    'categories',
                );

                $newConfigs['smlmo'] = $oldConfigs['smlmo'];
            }

            if (!empty($oldConfigs['startUpgradeAt'])) {
                $newConfigs['startUpgradeAt'] = $oldConfigs['startUpgradeAt'];
            }

            $this->saveConfigs($newConfigs);
        }
    }
}
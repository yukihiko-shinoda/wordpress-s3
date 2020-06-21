<?php
/**
 * Created by IntelliJ IDEA.
 * User: master
 * Date: 2018/12/27
 * Time: 12:34
 */
require_once __DIR__ . '/Replacement.php';
require_once __DIR__ . '/StrReplacement.php';
require_once __DIR__ . '/PregReplacement.php';

class ConfigReplacement
{
    public static function replace() {
        $replacesWpSettings = [
            //↓ To read by eval()
        '<?php'		                                                               => '',
            //↓ To replace only StaticPress
        'include_once( $plugin );'                                                 => "if (strcmp(\$plugin, WP_CONTENT_DIR.'/plugins/staticpress2019/plugin.php') === 0) {\n        include_once(WP_CONTENT_DIR.'/plugins/staticpress2019/plugin.php');\n    } else {\n        include_once( \$plugin );\n    }\n",
        'include_once(WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\');'    => self::createReplacementStaticPressPlugin(),
        ];
        $replacementWpSettings = new StrReplacement($replacesWpSettings,'ABSPATH . \'wp-settings.php\'');
        return $replacementWpSettings->renderReplacedCode();
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementStaticPressPlugin() {
        $replacesStaticPressPluginStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            '__FILE__' => 'WP_CONTENT_DIR . \'/plugins/staticpress2019/plugin.php\'',
            'require_once STATIC_PRESS_PLUGIN_DIR . \'includes/class-static-press.php\';' => self::createReplacementClassStaticPress(),
            // ↓ @see http://webfood.info/staticpress-s3/#feedindexhtmlurl
//            '\'replace_relative_uri\' ), 10, 2 );' => "'replace_relative_uri' ), 10, 2 );" . ConfigReplacement::loadPHPFile(__DIR__ . '/ReplacementForFixHref.php')
        ];
        return new StrReplacement($replacesStaticPressPluginStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementClassStaticPress() {
        $replacesClassStaticPressStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            'require_once STATIC_PRESS_PLUGIN_DIR . \'includes/controllers/class-static-press-ajax-fetch.php\';' => self::createReplacementAjaxFetch(),
        ];
        return new StrReplacement($replacesClassStaticPressStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static-press.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementAjaxFetch() {
        $replacesUrlCollectorStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            'require_once STATIC_PRESS_PLUGIN_DIR . \'includes/controllers/class-static-press-ajax-processor.php\';' => self::createReplacementAjaxProcessor(),
        ];
        return new StrReplacement($replacesUrlCollectorStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static-press-ajax-fetch.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementAjaxProcessor() {
        $replacesUrlCollectorStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            'require_once STATIC_PRESS_PLUGIN_DIR . \'includes/class-static-press-url-collector.php\';' => self::createReplacementUrlCollector(),
        ];
        return new StrReplacement($replacesUrlCollectorStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static-press-ajax-processor.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementUrlCollector() {
        $replacesUrlCollectorStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To stop to dump static files under wp-admin and wp-includes directory
            '$file_scanner_abspath->scan( \'/wp-admin/\', true ),' => '// $file_scanner_abspath->scan( \'/wp-admin/\', true ),',
            '$file_scanner_abspath->scan( \'/wp-includes/\', true ),' => '// $file_scanner_abspath->scan( \'/wp-includes/\', true ),',
        ];
        return new StrReplacement($replacesUrlCollectorStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static-press-url-collector.php\'');
    }

    /**
     * @param $file
     * @return string
     */
    public static function loadPHPFile($file) {
        return str_replace('<?php', '', file_get_contents($file));
    }
}

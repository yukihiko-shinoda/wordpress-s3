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
        '<?php'		                                                           => '',
            //↓ To replace only StaticPress and StaticPress S3
        'include_once( $plugin );'                                             => "if (strcmp(\$plugin, WP_CONTENT_DIR.'/plugins/staticpress2019/plugin.php') === 0) {\n        include_once(WP_CONTENT_DIR.'/plugins/staticpress2019/plugin.php');\n    } elseif (strcmp(\$plugin, WP_CONTENT_DIR.'/plugins/staticpress-s3/plugin.php') === 0) {\n        include_once(WP_CONTENT_DIR.'/plugins/staticpress-s3/plugin.php');\n    } else {\n        include_once( \$plugin );\n    }\n",
        'include_once(WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\');'    => self::createReplacementStaticPressPlugin(),
        'include_once(WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\');' => self::createReplacementStaticPressS3Plugin(),
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
            '__FILE__' => 'WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\'',
            'require(dirname(WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\').\'/includes/class-static_press.php\');' => self::createReplacementClassStaticPress(),
            // ↓ @see http://webfood.info/staticpress-s3/#feedindexhtmlurl
            '\'replace_relative_URI\'), 10, 2);' => "'replace_relative_URI'), 10, 2);" . ConfigReplacement::loadPHPFile(__DIR__ . '/ReplacementForFixHref.php')
        ];
        return new StrReplacement($replacesStaticPressPluginStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementStaticPressS3Plugin() {
        $replacesStaticPressS3PluginStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            '__FILE__' => 'WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\'',
            'require(dirname(WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\').\'/includes/class-S3_helper.php\');' => self::createReplacementClassS3Helper(),
        ];
        return new StrReplacement($replacesStaticPressS3PluginStr, 'WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementClassStaticPress() {
        $replacesClassStaticPressStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To read by eval()
            'str_replace(trailingslashit(ABSPATH), trailingslashit($this->get_site_url()), $static_file)' => 'str_replace(trailingslashit(dirname(ABSPATH)), trailingslashit($this->get_site_url()), $static_file)',
            //↓ To read by eval()
            'untrailingslashit(ABSPATH)' => 'untrailingslashit(dirname(ABSPATH))',
            //↓ To read by eval()
            '__FILE__' => 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static_press.php\'',
            //↓ To stop to dump static files under wp-admin and wp-includes directory
            '$this->scan_file(trailingslashit(ABSPATH).\'wp-admin/\'' => '//$this->scan_file(trailingslashit(ABSPATH).\'wp-admin/\'',
            //↓ To stop to upload static files under wp-admin and wp-includes directory to S3
            '$this->scan_file(trailingslashit(ABSPATH).\'wp-includes/\'' => '//$this->scan_file(trailingslashit(ABSPATH).\'wp-includes/\'',
        ];
        return new StrReplacement($replacesClassStaticPressStr, 'WP_CONTENT_DIR.\'/plugins/staticpress2019/includes/class-static_press.php\'');
    }

    /**
     * @return StrReplacement
     */
    public static function createReplacementClassS3Helper() {
        $replacesClassS3HelperStr = [
            //↓ To read by eval()
            '<?php' => '',
            //↓ To run on every Linux OS
            '/usr/share/misc/magic' => WP_STATIC_PRESS_S3_MAGIC_FILE_PATH,
            //↓ To read by eval()
            '__FILE__' => 'WP_CONTENT_DIR.\'/plugins/staticpress-s3/includes/class-S3_helper.php\'',
        ];
        return new StrReplacement($replacesClassS3HelperStr, 'WP_CONTENT_DIR.\'/plugins/staticpress-s3/includes/class-S3_helper.php\'');
    }

    /**
     * @param $file
     * @return string
     */
    public static function loadPHPFile($file) {
        return str_replace('<?php', '', file_get_contents($file));
    }
}

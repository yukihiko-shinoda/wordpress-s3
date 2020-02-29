<?php
/**
 * Created by IntelliJ IDEA.
 * User: master
 * Date: 2018/12/27
 * Time: 13:25
 */

require_once __DIR__ . '/../../config/ConfigReplacement.php';
use PHPUnit\Framework\TestCase;

define( 'ABSPATH', __DIR__ . '/../../web/wp/' );
$root_dir = dirname(__DIR__ . '/../../../');
$webroot_dir = $root_dir . '/web';
Env::init();
define('CONTENT_DIR', '/app');
define('WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR);
define('WP_STATIC_PRESS_S3_MAGIC_FILE_PATH', env('WP_STATIC_PRESS_S3_MAGIC_FILE_PATH') ?: '/usr/share/misc/magic');

class ConfigReplacementTest extends TestCase {
    public function testReplace() {
        $actual = eval(ConfigReplacement::replace());
        $actualLines = explode("\n", $actual);
        $expected1 = 'foreach ( wp_get_active_and_valid_plugins() as $plugin ) {';
        $expected2 = 'wp_register_plugin_realpath( $plugin );';
        $expected3 = 'if (strcmp($plugin, WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\') === 0) {';
        $expected4 = ', file_get_contents(WP_CONTENT_DIR.\'/plugins/staticpress2019/plugin.php\'))';
        $expected5 = ', file_get_contents(WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\'))';
        $expected6 = 'unset( $plugin );';

        $lineNumber1 = $this->searchLine($actualLines, $expected1);
        $lineNumber2 = $this->searchLine($actualLines, $expected2);
        $lineNumber3 = $this->searchLine($actualLines, $expected3);
        $lineNumber4 = $this->searchLine($actualLines, $expected4);
        $lineNumber5 = $this->searchLine($actualLines, $expected5);
        $lineNumber6 = $this->searchLine($actualLines, $expected6);
        $this->assertNotNull($lineNumber1);
        $this->assertNotNull($lineNumber2);
        $this->assertNotNull($lineNumber3);
        $this->assertNotNull($lineNumber4);
        $this->assertNotNull($lineNumber5);
        $this->assertNotNull($lineNumber6);
        $this->assertGreaterThan($lineNumber1, $lineNumber2);
        $this->assertGreaterThan($lineNumber2, $lineNumber3);
        $this->assertGreaterThan($lineNumber3, $lineNumber4);
        $this->assertGreaterThan($lineNumber4, $lineNumber5);
        $this->assertGreaterThan($lineNumber5, $lineNumber6);
    }

    public function testCreateReplacementStaticPressPlugin() {
        $renderedReplacedCode = ConfigReplacement::createReplacementStaticPressPlugin()->renderReplacedCode();
        $actualLines1 = explode("\n", $renderedReplacedCode);
        $expected1 = '\\\'href=""\\\',';
        $lineNumber1 = $this->searchLine($actualLines1, $expected1);
        $this->assertNotNull($lineNumber1);
        $actual = eval($renderedReplacedCode);

        $actualLines2 = explode("\n", $actual);
        $expected2 = 'add_filter( \'StaticPress::put_content\', array( $staticpress, \'replace_relative_URI\' ), 10, 2 );';
        $expected3 = 'add_action(\'StaticPress::file_put\', \'replace_home_url\', 1);';
        $expected4 = 'add_filter( \'https_local_ssl_verify\', \'__return_false\' );';
        $lineNumber2 = $this->searchLine($actualLines2, $expected2);
        $lineNumber3 = $this->searchLine($actualLines2, $expected3);
        $lineNumber4 = $this->searchLine($actualLines2, $expected4);
        $this->assertNotNull($lineNumber2);
        $this->assertNotNull($lineNumber3);
        $this->assertNotNull($lineNumber4);
        $this->assertGreaterThan($lineNumber2, $lineNumber3);
        $this->assertGreaterThan($lineNumber3, $lineNumber4);
    }

    public function testCreateReplacementStaticPressS3Plugin() {
        $actual = eval(ConfigReplacement::createReplacementStaticPressS3Plugin()->renderReplacedCode());
        $actualLines = explode("\n", $actual);
        $expected1 = 'if (!class_exists(\'S3_helper\'))';
        $expected2 = ', file_get_contents(WP_CONTENT_DIR.\'/plugins/staticpress-s3/includes/class-S3_helper.php\'))';
        $expected3 = 'require(dirname(WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\').\'/includes/class-staticpress_s3_admin.php\');';
        $expected4 = 'require(dirname(WP_CONTENT_DIR.\'/plugins/staticpress-s3/plugin.php\').\'/includes/class-staticpress_s3.php\');';
        $lineNumber1 = $this->searchLine($actualLines, $expected1);
        $lineNumber2 = $this->searchLine($actualLines, $expected2);
        $lineNumber3 = $this->searchLine($actualLines, $expected3);
        $lineNumber4 = $this->searchLine($actualLines, $expected4);
        $this->assertNotNull($lineNumber1);
        $this->assertNotNull($lineNumber2);
        $this->assertNotNull($lineNumber3);
        $this->assertNotNull($lineNumber4);
        $this->assertGreaterThan($lineNumber1, $lineNumber2);
        $this->assertGreaterThan($lineNumber2, $lineNumber3);
        $this->assertGreaterThan($lineNumber3, $lineNumber4);
    }

    public function testCreateReplacementClassStaticPress() {
        $actual = eval(ConfigReplacement::createReplacementClassStaticPress()->renderReplacedCode());
        $actualLines = explode("\n", $actual);
        $expected1 = 'foreach ($static_files as $static_file){';
        $expected2 = '$static_file_url = str_replace(trailingslashit(dirname(ABSPATH)), trailingslashit($this->get_site_url()), $static_file);';
        $expected3 = '\'url\' => apply_filters(\'StaticPress::get_url\', $static_file_url),';
        $expected4 = '$file_source = untrailingslashit(dirname(ABSPATH)) . $url;';
        $expected5 = '$file_source = untrailingslashit(dirname(ABSPATH)) . $url[\'url\'];';
        $expected6 = '//$this->scan_file(trailingslashit(ABSPATH).\'wp-admin/\'';
        $expected7 = '//$this->scan_file(trailingslashit(ABSPATH).\'wp-includes/\'';
        $lineNumber1 = $this->searchLine($actualLines, $expected1);
        $lineNumber2 = $this->searchLine($actualLines, $expected2);
        $lineNumber3 = $this->searchLine($actualLines, $expected3);
        $lineNumber4 = $this->searchLine($actualLines, $expected4);
        $lineNumber5 = $this->searchLine($actualLines, $expected5);
        $lineNumber6 = $this->searchLine($actualLines, $expected6);
        $lineNumber7 = $this->searchLine($actualLines, $expected7);
        $this->assertNotNull($lineNumber1);
        $this->assertNotNull($lineNumber2);
        $this->assertNotNull($lineNumber3);
        $this->assertNotNull($lineNumber4);
        $this->assertNotNull($lineNumber5);
        $this->assertNotNull($lineNumber6);
        $this->assertNotNull($lineNumber7);
        $this->assertGreaterThan($lineNumber1, $lineNumber2);
        $this->assertGreaterThan($lineNumber2, $lineNumber3);
        $this->assertGreaterThan($lineNumber4, $lineNumber5);
    }

    public function testCreateReplacementClassS3Helper() {
        $actual = eval(ConfigReplacement::createReplacementClassS3Helper()->renderReplacedCode());
        $actualLines = explode("\n", $actual);
        $expected1 = env('WP_STATIC_PRESS_S3_MAGIC_FILE_PATH') ?: '/usr/share/misc/magic';
        $lineNumber1 = $this->searchLine($actualLines, $expected1);
        $this->assertNotNull($lineNumber1);
    }

    /**
     * @param $lines
     * @param $expected
     * @return int
     */
    private function searchLine($lines, $expected)
    {
        $lineNumber = 1;
        foreach ($lines as $line) {
            if (mb_strpos($line, $expected) !== false) {
                return $lineNumber;
            }
            $lineNumber++;
        }
        return null;
    }
}

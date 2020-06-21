<?php
add_action('StaticPress::file_put', 'replace_home_url', 1);
function replace_home_url($file_dest){
    if (is_dir($file_dest)) {
        return;
    }
    $magic_file = env('WP_STATIC_PRESS_S3_MAGIC_FILE_PATH') ?: '/usr/share/misc/magic';
    $info = file_exists($magic_file)
        ? new FInfo(FILEINFO_MIME_TYPE, $magic_file)
        : new FInfo(FILEINFO_MIME_TYPE);
    $mime_type = file_exists($file_dest) ? $info->file($file_dest) : false;
    if ( substr( $mime_type, 0, 4 ) !== 'text' ) {
        return;
    }
    $buff = file_get_contents($file_dest);
    if ($buff === false) {
        return;
    }
    $replace = urlencode(static_press_admin::static_url());
    $content = str_replace(
        [
            'href=""',
            urlencode(home_url()),
        ], [
            'href="/"',
            $replace,
        ],
        $buff
    );
    $result = file_put_contents($file_dest, $content);
    if ($result === false) {
        throw new UnexpectedValueException($file_dest);
    }
}

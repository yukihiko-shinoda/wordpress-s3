<?php
add_action('StaticPress::file_put', 'replace_home_url', 1);
function replace_home_url($file_dest){
    if (is_dir($file_dest)) {
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

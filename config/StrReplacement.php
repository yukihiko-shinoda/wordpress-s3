<?php
/**
 * Created by IntelliJ IDEA.
 * User: master
 * Date: 2018/08/20
 * Time: 14:37
 */

require_once __DIR__ . '/Replacement.php';

class StrReplacement extends Replacement
{
    private $replaces;
    public function __construct($replaces, $content)
    {
        $this->replaces = $replaces;
        parent::__construct($content);
    }

    protected function renderCodeToReplace() {
        $stringContents = $this->buildStringContent();
        $stringSearch = $this->renderArrayAsString(array_keys($this->replaces));
        $stringReplace = $this->renderArrayAsString(array_values($this->replaces));

        return 'str_replace('.$stringSearch.', '.$stringReplace.', '.$stringContents.')';
    }
}

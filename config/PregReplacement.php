<?php
/**
 * Created by IntelliJ IDEA.
 * User: master
 * Date: 2018/08/20
 * Time: 14:33
 */

require_once __DIR__ . '/Replacement.php';

class PregReplacement extends Replacement
{
    private $replaces;
    private $limit;
    public function __construct($replaces, $content, $limit)
    {
        $this->replaces = $replaces;
        $this->limit = $limit;
        parent::__construct($content);
    }

    protected function renderCodeToReplace() {
        $stringContents = $this->buildStringContent();
        $stringSearch = $this->renderArrayAsString(array_keys($this->replaces));
        $stringReplace = $this->renderArrayAsString(array_values($this->replaces));

        return 'preg_replace('.$stringSearch.', '.$stringReplace.', '.$stringContents.', '.$this->limit.')';
    }
}

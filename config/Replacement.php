<?php
/**
 * Created by IntelliJ IDEA.
 * User: master
 * Date: 2018/08/20
 * Time: 12:39
 */

class Replacement
{
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function renderReplacedCode() {
        return 'return ' . $this->renderCodeToReplace() . ';';
    }

    protected function renderCodeToReplace() {
        return 'require_once(' . $this->content . ')';
    }

    public function wrapBySingleQuoteWithSemicolon($stringContents) {
        return '\'' . $this->escape($stringContents) . ';\'';
    }

    public function wrapBySingleQuote($stringContents) {
        return '\'' . $this->escape($stringContents) . '\'';
    }
    /**
     * @return string
     */
    public function buildStringContent()
    {
        switch (true) {
            case $this->content instanceof Replacement:
                /** @var Replacement $replacement */
                $replacement = $this->content;
                return $replacement->renderCodeToReplace();
            default:
                return 'file_get_contents(' . $this->content . ')';
        }
    }

    protected function renderArrayAsString($array) {
        $renderedString = '';
        foreach ($array as $string) {
            switch (true) {
                case $string instanceof Replacement && !is_subclass_of($string, Replacement::class):
                    $renderedString = $renderedString.$this->wrapBySingleQuoteWithSemicolon($string->renderCodeToReplace()).',';
                    break;
                case $string instanceof Replacement:
                    $renderedString = $renderedString.$this->wrapBySingleQuoteWithSemicolon('eval('.$string->renderCodeToReplace().')').',';
                    break;
                default:
                    $renderedString = $renderedString.$this->wrapBySingleQuote($string).',';
                    break;
            }
        }
        return '['.$renderedString.']';
    }

    private function escape($stringContents) {
        return str_replace('\'', '\\\'', str_replace('\\', '\\\\', $stringContents));
    }
}

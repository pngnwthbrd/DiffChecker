<?php

class DiffChecker
{
    public $diff;

    const LINE_BREAK_EXPR = '/\R/';
    const WHITE_SPACE_EXPR = '/\s/';

    public function __construct()
    {
        $this->diff = "";
    }

    public function compare($left, $right)
    {
        $lines = array();
        $lines = $this->getSplitArray($left, $right, self::LINE_BREAK_EXPR);

        $new_leftlines = array();
        $new_rightlines = array();

        reset($lines['left']);
        reset($lines['right']);
        $line_runner = $this->getRunner($lines['left'], $lines['right']);
        $line_start = 0;

        while ($line_start < $line_runner)
        {
            $line_number = '<span style="padding:0 15px;background:#f0f0f0;color:c1c1c1;margin:0 5px">' . ($line_start +1) . '</span>';
            $leftline = current($lines['left']);
            $rightline = current($lines['right']);
            $leftline = strip_tags($leftline);
            $rightline = strip_tags($rightline);
            if ($this->isDifferent($leftline, $rightline)) {
                $words = array();
                $words = $this->getSplitArray($leftline, $rightline, self::WHITE_SPACE_EXPR);
                $new_leftwords = array();
                $new_rightwords = array();

                $leftwords = array_filter($words['left'], array($this, 'setEmptyToFalse'));
                $rightwords = array_filter($words['right'], array($this, 'setEmptyToFalse'));

                reset($leftwords);
                reset($rightwords);
                $word_runner = $this->getRunner($leftwords, $rightwords);
                $word_start = 0;
                while ($word_start < $word_runner) {
                    $leftword = current($leftwords);
                    $rightword = current($rightwords);
                    if ($leftword == false) {
                        array_push($new_leftwords, $leftword);
                        next($leftwords);
                    }
                    if ($rightword == false) {
                        array_push($new_rightwords, $rightword);
                        next($rightwords);
                    }
                    if ($leftword !== $rightword) {
                        $leftword = '<span ' . (($leftword !== "&nbsp;") ? 'style="background:#FF6464;"' : '') . '>' . $leftword . '</span>';
                        $rightword = '<span ' . (($rightword !== "&nbsp;") ? 'style="background:#6BFF6B;"' : '') . '>' . $rightword . '</span>';
                        array_push($new_leftwords, $leftword);
                        array_push($new_rightwords, $rightword);
                    } else {
                        array_push($new_leftwords, $leftword);
                        array_push($new_rightwords, $rightword);
                    }

                    next($leftwords);
                    next($rightwords);
                    $word_start++;
                }

                $new_leftline = '<span style="background:#FFB9B9;">' . implode(" ", $new_leftwords) . '</span>';
                $new_rightline = '<span style="background:#C8F9C8;">' . implode(" ", $new_rightwords) . '</span>';
                array_push($new_leftlines, $line_number . $new_leftline);
                array_push($new_rightlines, $line_number . $new_rightline);
            } else {
                array_push($new_leftlines, $line_number . $leftline);
                array_push($new_rightlines, $line_number . $rightline);
            }

            next($lines['left']);
            next($lines['right']);
            $line_start++;
        }

        $this->createDiffHtml($new_leftlines, $new_rightlines);
    }

    protected function getSplitArray($left, $right, $expr)
    {
        $leftArr = preg_split($expr, $left);
        $rightArr = preg_split($expr, $right);

        return array(
            'left' => $leftArr,
            'right' => $rightArr
        );
    }

    protected function getRunner($left, $right)
    {
        return (count($left) >= count($right)) ?
        count($left) : count($right);
    }

    protected function setEmptyToFalse($var)
    {
        return (empty($var)) ? false : $var;
    }

    protected function isDifferent($left, $right)
    {
        return $left !== $right;
    }

    protected function createDiffHtml($left, $right)
    {
        $output = '
        <table width="100%">
        <tr>
        <td>' . $this->createSide($left) . '</td>
        <td>' . $this->createSide($right) . '</td>
        </tr>
        </table>';

        $this->diff = $output;
    }

    protected function createSide($side)
    {
        return nl2br(implode("\n", $side));
    }
}

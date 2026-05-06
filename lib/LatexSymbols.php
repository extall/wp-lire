<?php

/**
 * ----------------------------------------------
 * LatexSymbols :: replaces special LaTeX symbols
 * ----------------------------------------------
 *
 * @author Aleksei Tepljakov
 * @version 0.1
 */
class LatexSymbols
{
    // replaceLatexSymbols() :: special symbol replacement
    public function replaceLatexSymbols($text)
    {
        // Normal text replace
        $text = str_replace('\\&', '&amp;', $text);
        $text = str_replace('\\#', '&#35;', $text);
        $text = str_replace('\\$', '&#36;', $text);
        $text = str_replace('\\%', '&#37;', $text);

        return $text;
    }
    
    // precodeTextBackslash() :: Find and replace all occurences of "\" within <pre> and <code> tags
    public function precodeTextBackslash($text)
    {
        $result = array();
        preg_match_all('%(?s)<(pre|code)>.*?<\/(\1)>%i', $text, $result, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($result[0]); $i++)
        {
            $part = $result[0][$i];
            $part = str_replace('\\', '\\textbackslash', $part);    // Replace backslashes
            $part = str_replace('\$', '&#36;', $part);   // Prevent inline math to be processed
            
            $text = str_replace($result[0][$i], $part, $text);
        }
        return $text;
    }

    // replaceTextBackslash() :: special function to replace all occurences of \textbackslash and
    public function replaceTextBackslash($text)
    {
        return str_replace('\\textbackslash', '&#92;', $text);
    }

    // removeCurlyBraces() :: smart removing of curly braces with respect to inline math
    public function removeCurlyBraces($text)
    {
        $dollarFound = false;
        $i = 0;
        while ($i < strlen($text))
        {
            if ($text[$i] == '$')
            {
                if (!$dollarFound)
                {
                    // Math mode on
                    $dollarFound = true;
                } else
                {
                    // Math mode off
                    $dollarFound = false;
                }
            }
            // In case of different symbol...
            else
            {
                // ...when not in math mode, remove curly braces
                if (($text[$i] == '{' ||
                        $text[$i] == '}') &&
                        !$dollarFound)
                {
                    $text = substr($text, 0, $i) . substr($text, $i + 1);
                    $i--;
                }
            }
            $i++;
        }
        return $text;
    }
    
    // strNoMathReplace() :: str_replace outside inline math
    public function strNoMathReplace($what, $to, $text)
    {
        // If inline math found, use special routine for symbol replacement
        if (strpos($text, "\$") !== false)
        {
            $dollarFound = false;
            $i = 0;
            while ($i < strlen($text) - strlen($what))
            {
                if ($text[$i] == '$')
                {
                    if (!$dollarFound)
                    {
                        // Math mode on
                        $dollarFound = true;
                    } else
                    {
                        // Math mode off
                        $dollarFound = false;
                    }
                }
                // In case of different symbol...
                else
                {
                    // ...when not in math mode, remove curly braces
                    if (strcmp(substr($text, $i, strlen($what)), $what) === 0 &&
                            !$dollarFound)
                    {
                        $text = substr($text, 0, $i) .
                                $to .
                                substr($text, $i + strlen($what));
                        $i = $i + strlen($to) - 1;
                    }
                }
                $i++;
            }
        } else
        {
            return str_replace($what, $to, $text);
        }
        return $text;
    }

    // replaceLatexChars() :: special character conversion
    public function replaceLatexChars($text)
    {
        $text = preg_replace('/([^\\\\])~/', '\\1&nbsp;', $text);

        // Preserve case
        if (strpos($text, '\\') === false && strpos($text, '{') === false)
            return $text;

        // URLs
        $text = preg_replace('/\\\\url\{(.*)\}/U', '<a href="\\1">\\1</a>', $text);

        // i, j characters
        $text = preg_replace('/\\\\([ij])/i', '\\1', $text);

        // Acute accent
        $text = $this->char2html($text, "'", 'a', "acute");
        $text = $this->char2html($text, "'", 'e', "acute");
        $text = $this->char2html($text, "'", 'i', "acute");
        $text = $this->char2html($text, "'", 'o', "acute");
        $text = $this->char2html($text, "'", 'u', "acute");
        $text = $this->char2html($text, "'", 'y', "acute");
        $text = $this->char2html($text, "'", 'n', "acute");

        // Grave accent
        $text = $this->char2html($text, '`', 'a', "grave");
        $text = $this->char2html($text, '`', 'e', "grave");
        $text = $this->char2html($text, '`', 'i', "grave");
        $text = $this->char2html($text, '`', 'o', "grave");
        $text = $this->char2html($text, '`', 'u', "grave");

        // Tilde accent
        $text = $this->char2html($text, '~', 'a', "tilde");
        $text = $this->char2html($text, '~', 'n', "tilde");
        $text = $this->char2html($text, '~', 'o', "tilde");

        // Umlaut accent
        $text = $this->char2html($text, '"', 'a', "uml");
        $text = $this->char2html($text, '"', 'e', "uml");
        $text = $this->char2html($text, '"', 'i', "uml");
        $text = $this->char2html($text, '"', 'o', "uml");
        $text = $this->char2html($text, '"', 'u', "uml");
        $text = $this->char2html($text, '"', 'y', "uml");

        // Circumflex accent
        $text = $this->char2html($text, '^', 'a', "circ");
        $text = $this->char2html($text, '^', 'e', "circ");
        $text = $this->char2html($text, '^', 'i', "circ");
        $text = $this->char2html($text, '^', 'o', "circ");
        $text = $this->char2html($text, '^', 'u', "circ");

        // Misc
        $text = $this->char2html($text, '.', 'a', "ring");

        $text = $this->char2html($text, 'c', 'c', "cedil");

        $text = $this->strNoMathReplace('\\ae', '&aelig;', $text);
        $text = $this->strNoMathReplace('\\ss', '&szlig;', $text);

        $text = $this->strNoMathReplace('\\o', '&oslash;', $text);
        $text = $this->strNoMathReplace('\\O', '&Oslash;', $text);
        $text = $this->strNoMathReplace('\\&', '&amp;', $text);

        // Several characters from the Latin Extended set (as UTF entities)
        $text = $this->char2utf($text, 'v', 'C', '0268');
        $text = $this->char2utf($text, 'v', 'c', '0269');
        $text = $this->char2utf($text, 'v', 'S', '0352');
        $text = $this->char2utf($text, 'v', 's', '0353');

        // Acute
        $text = $this->char2utf($text, "'", 'N', '0323');
        $text = $this->char2utf($text, "'", 'n', '0324');
        $text = $this->char2utf($text, "'", 'S', '0346');
        $text = $this->char2utf($text, "'", 's', '0347');
        $text = $this->char2utf($text, "'", 'Z', '0377');
        $text = $this->char2utf($text, "'", 'z', '0378');

        // Ogonek
        $text = $this->char2utf($text, 'k', 'A', '0260');
        $text = $this->char2utf($text, 'k', 'a', '0261');

        $text = $this->char2utf($text, 'c', 'E', '0280');
        $text = $this->char2utf($text, 'c', 'e', '0281');

        $text = $this->char2utf($text, '.', 'Z', '0379');
        $text = $this->char2utf($text, '.', 'z', '0380');

        $text = $this->strNoMathReplace('\\L', '&#0322;', $text);
        $text = $this->strNoMathReplace('\\l', '&#0322;', $text);

        $text = $this->strNoMathReplace('\\AA', '&#0197;', $text);
        $text = $this->strNoMathReplace('\\aa', '&#0229;', $text);

        // Encode as UTF-8 (utf8_encode() was deprecated in PHP 8.2)
        if (function_exists('mb_convert_encoding'))
        {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }

        return $text;
    }

    // char2html() :: converts HTML-entity convertible characters
    private function char2html($text, $latexmodifier, $char, $entityfragment)
    {
        $text = $this->strNoMathReplace('\\' . $latexmodifier . $char, '&' . $char . '' . $entityfragment . ';', $text);
        $text = $this->strNoMathReplace('\\' . $latexmodifier . '{' . $char . '}', '&' . $char . '' . $entityfragment . ';', $text);
        $text = $this->strNoMathReplace('\\' . $latexmodifier . strtoupper($char), '&' . strtoupper($char) . '' . $entityfragment . ';', $text);
        $text = $this->strNoMathReplace('\\' . $latexmodifier . '{' . strtoupper($char) . '}', '&' . strtoupper($char) . '' . $entityfragment . ';', $text);
        return $text;
    }

    // char2utf() :: converts UTF-style characters (e.g. extended latin)
    private function char2utf($text, $latexmodifier, $char, $entityfragment)
    {
        $text = $this->strNoMathReplace('\\' . $latexmodifier . $char, '&#' . $entityfragment . ';', $text);
        $text = $this->strNoMathReplace('\\' . $latexmodifier . '{' . $char . '}', '&#' . $entityfragment . ';', $text);
        return $text;
    }
}

?>

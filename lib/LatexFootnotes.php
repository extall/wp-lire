<?php

/**
 * -------------------------------------------------
 * LatexFootnotes :: takes care of in-text footnotes
 * -------------------------------------------------
 *
 * @author Aleksei Tepljakov
 * @version 0.1
 */

require_once 'LatexUtils.php';

class LatexFootnotes
{
    // parseFootnotes() :: parse in-text footnotes and put them
    // where a \footnotes{} command is encountered
    public function parseFootnotes($text)
    {
        $footNotes = '<div class="' . FOOTNOTE_REF_STYLE . '"><ul>';
        
        // Isolate all footnotes
        $result = LatexUtils::delimitedMatch("{", $text, "\\footnote", DELIM_FULL_MATCHES_ARRAY);
        for ($i = 0; $i < count($result); $i++)
        {
            $footnote = $result[$i]["full_match"];
            $footnoteText = $result[$i]["expression"];
            
            $noteNum = $i+1;
            $refId = FOOTNOTE_REF_STYLE . 'Ref' . $noteNum;
            $noteId = FOOTNOTE_REF_STYLE . $noteNum;
            
            // Replace reference
            $text = str_replace($footnote, 
                    str_replace('#', '<a id="' . $refId . '" name="' . $refId . '" href="#' . $noteId . '">' . $noteNum . '</a>', FOOTNOTE_REF_FORMAT),
                    $text);
            
            // Add note to footnote list
            $footNotes .= '<li>' . str_replace('#', 
                    '<a id="' . $noteId . '" name="' . $noteId . '" href="#' . $refId . '">' . $noteNum . '</a>',
                    FOOTNOTE_REF_FORMAT) .
                    ' ' . $footnoteText . '</li>';
        }
        
        $footNotes .= '</ul></div>';
        
        // Replace \footnotes{}
        return preg_replace('/\\\\footnotes(?:\{\})?/', $footNotes, $text);
    }
}

?>

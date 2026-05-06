<?php

/**
 * -------------------------------------------------
 * LatexSidenotes :: takes care of sidenotes
 * -------------------------------------------------
 *
 * @author Aleksei Tepljakov
 * @version 0.1
 */

require_once 'LatexUtils.php';

class LatexSidenotes
{
    // parsesidenotes() :: parse in-text sidenotes and put them
    // where a \sidenotes{} command is encountered
    public function parseSidenotes($text)
    {   
        // Isolate all enumerated and plain sidenotes
        $result = LatexUtils::delimitedMatch("{", $text, "\\sidenote", DELIM_FULL_MATCHES_ARRAY);
        for ($i = 0; $i < count($result); $i++)
        {
            $sidenote = $result[$i]["full_match"];
            $sidenoteText = $result[$i]["expression"];
            
            $noteNum = $i+1;
            $refId = SIDENOTE_REF_STYLE . 'Ref' . $noteNum;
            $noteId = SIDENOTE_REF_STYLE . $noteNum;
            
            // Generate the sidenote
			$sideNoteNumber = str_replace('#', $noteNum, SIDENOTE_REF_FORMAT);
            $text = str_replace($sidenote, 
                     $sideNoteNumber . "<span class='" . SIDENOTE_REF_STYLE . "'>" . $sideNoteNumber . $sidenoteText . "</span>",
                    $text);
		}
		
		// Isolated all plain sidenotes
		$result = LatexUtils::delimitedMatch("{", $text, "\\sidenote*", DELIM_FULL_MATCHES_ARRAY);
		for ($i = 0; $i < count($result); $i++)
        {
            $sidenote = $result[$i]["full_match"];
            $sidenoteText = $result[$i]["expression"];
            
            // Generate the sidenote
            $text = str_replace($sidenote, 
                     "<span class='" . SIDENOTE_REF_STYLE . "'>" . $sidenoteText . "</span>",
                    $text);
		}
		
		return $text;
	}
}

?>

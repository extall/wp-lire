<?php

/* ***********************************************
 * LatexUtils: A helper class for TeX code parsing
 * ***********************************************
 * 
 * @author Aleksei Tepljakov
 */

define("DELIM_FULL_MATCHES_ARRAY", 1);           // Output an array with detailed information about matches
define("DELIM_OUTPUT_FULL_MATCHES_ONLY", 2);     // Output an array containing full matches only

class LatexUtils
{
    const KNOWN_DELIMS = "{}()[]<>";

    // Match a set of expressions, that adhere to the requested
    // scheme with a particular set of delimiters
    public static function delimitedMatch($delim, $text, $pretext = "", $output_format = DELIM_FULL_MATCHES_ARRAY)
    {
        // If delimiters not given, try to find a matching delimiter
        if ($delim === "") {
            // Empty delimiter passed
            return false;
        } elseif (strlen($delim) < 2) {
            $delim_index = strpos(self::KNOWN_DELIMS, $delim);
            if ($delim_index === false) {
                // Failed to determine delimiter
                return false;
            } else {
                $delims = substr(self::KNOWN_DELIMS, $delim_index, 1) .
                    substr(self::KNOWN_DELIMS, $delim_index + 1, 1);
            }
        } else {
            // Take first two symbols as delimiters
            $delims = substr($delim, 0, 2);
        }

        // Delimiters and opening text to search
        $openDelim = $delims[0];
        $closeDelim = $delims[1];
        $preSearch = $pretext . $openDelim;

        // Construct the $matches array
        $matches = array();

        // Begin parsing the text
        $init_pos = 0;
        $reachedEnd = false;

        while (false !== ($pos = strpos($text, $preSearch, $init_pos)) && !$reachedEnd) {
            $expr_begin = $pos + strlen($preSearch);
            $expr_end = false;

            $search_pos = $expr_begin;
            $openFound = 1;
            $match_found = false;

            while ($search_pos < strlen($text) && !$match_found) {

                $current_char = $text[$search_pos++];
                if ($current_char == $openDelim) {
                    // A new opening delimiter was found
                    $openFound++;
                } elseif ($current_char == $closeDelim) {
                    $openFound--;
                    if ($openFound == 0) {
                        // Closing delimiter relative to the first one found
                        $init_pos = $search_pos;
                        $match_found = true;

                        // Expression position
                        $expr_end = $search_pos - 1;

                        $expression = substr($text, $expr_begin, $expr_end - $expr_begin);

                        // Populate the matches array
                        $matches[] = array(
                            "full_match" => $pretext . $openDelim . $expression . $closeDelim,
                            "expression" => $expression,
                            "match_start" => $pos,
                            "match_end" => $search_pos);
                    }
                }
            }

            // Check whether the parser has reached the end of the text
            if ($search_pos >= strlen($text)) {
                $reachedEnd = true;
            }

        }

        // Check output format
        if ($output_format & DELIM_OUTPUT_FULL_MATCHES_ONLY) {
            $output_matches_only = array();
            foreach ($matches as $match) {
                $output_matches_only[] = $match["full_match"];
            }
            $matches = $output_matches_only;
        }

        return $matches;
    }
}

?>

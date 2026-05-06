<?php

/**
 * The following functions provide basic in-text and bibliography citation
 * number formatting, e.g. [1] [2, 3].
 * 
 * @author Alexei Teplyakov
 * @version 0.1
 */

function BibtexCiteCitationFormat($keys, $citationIndex, &$bibtexBib)
{
    $allKeys = explode(',', $keys);
    $citation = '[';
    for ($n = 0; $n < count($allKeys); $n++)
    {
        if (array_key_exists(trim($allKeys[$n]), $bibtexBib))
        {
            $citation .= '<a class="' . LATEX_REFERENCE . '" href="#' .
                    BIBTEX_PREFIX . trim($allKeys[$n]) . '">' . $citationIndex[trim($allKeys[$n])] . '</a>';
        } else
        {
            $citation .= '?';
        }

        if ($n < count($allKeys) - 1)
        {
            $citation .= ', ';
        }
    }
    $citation .= ']';
    return $citation;
}

function BibtexCiteCitationPlainFormat($keys, $citationIndex, &$bibtexBib)
{
    $allKeys = explode(',', $keys);
    $citation = '[';
    for ($n = 0; $n < count($allKeys); $n++)
    {
        $citation .= $citationIndex[trim($allKeys[$n])];
        if ($n < count($allKeys) - 1)
        {
            $citation .= ', ';
        }
    }
    $citation .= ']';
    return $citation;
}

?>

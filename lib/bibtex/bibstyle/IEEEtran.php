<?php

/**
 * The following functions provide basic IEEEtran-like bibliography entry
 * formatting. The main function to be called is:
 * BibtexCiteEntryFormat($entry), where $entry is an array, which is an element
 * of a structure with arrays provided by Structures_Bibtex class
 * 
 * @author Alexei Teplyakov
 * @version 0.1
 */

require_once ROOTDIR . 'LatexSymbols.php';

function BibtexCiteEntryFormat($entry)
{
    // Load character/symbol converters
    $latexSymbols = new LatexSymbols();
    
    // Entry HTML
    $entryText = "";
    
    // Authors
    $authors = (array_key_exists('author', $entry)) ?
            BibtexCiteEntryFormatAuthors($entry['author']) :
            '';
    
    // Title
    $title = (array_key_exists('title', $entry)) ?
            BibtexCiteEntryFormatTitle($entry) :
            '';

    // Further content depends on type of entry
    switch ($entry['entryType'])
    {
        case "inproceedings":
        case "conference":
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '&ldquo;' . $title . ',&rdquo;';       // Title  
            $entryText .= (array_key_exists('booktitle', $entry)) ?    // Proceedings
                    ' in <em>' . $entry['booktitle'] . '</em>,' :
                    '';
            if (array_key_exists('address', $entry) &&                 // Address and publisher
                    array_key_exists('publisher', $entry))
            {
                $entryText = BibtexCiteEntryReplaceComma($entryText) . ' ' . 
                        $entry['address'] . ': ' . $entry['publisher'] . ',';
            } else
            {
                if (array_key_exists('address', $entry))               // Address
                {
                    $entryText .= ' ' . $entry['address'] . ',';
                }
                if (array_key_exists('publisher', $entry))             // Publisher
                {
                    $entryText = BibtexCiteEntryReplaceComma($entryText) .
                            ' ' . $entry['publisher'] . ',';
                }
            }
            $entryText .= (array_key_exists('volume', $entry)) ?       // Volume
                    ' vol. ' . $entry['volume'] . ',' :
                    '';
            $entryText .= (array_key_exists('number', $entry)) ?       // Number
                    ' no. ' . $entry['number'] . ',' :
                    '';
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' ' . $entry['year'] . ',' :
                    '';
            $entryText .= (array_key_exists('pages', $entry)) ?        // Pages
                    ' pp. ' . str_replace(array('--', '-'), 
                                          array('&ndash;', '&ndash;'),
                                          $entry['pages']) . ',' :
                    '';
            break;

        case "book":
        case "inbook":
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '<em>' . $title . '</em>,';            // Title
            $entryText .= (array_key_exists('edition', $entry)) ?      // Edition
                    ' ' . BibtexCiteEntryReplaceEdition($entry['edition']) . ' ed,' :
                    '';
            $entryText .= (array_key_exists('series', $entry)) ?       // Series
                    ' ser. ' . $entry['series'] . ',' :
                    '';
            if (array_key_exists('address', $entry) &&                 // Address and publisher
                    array_key_exists('publisher', $entry))
            {
                $entryText = BibtexCiteEntryReplaceComma($entryText) . ' ' . 
                        $entry['address'] . ': ' . $entry['publisher'] . ',';
            } else
            {
                if (array_key_exists('address', $entry))               // Address
                {
                    $entryText .= ' ' . $entry['address'] . ',';
                }
                if (array_key_exists('publisher', $entry))             // Publisher
                {
                    $entryText = BibtexCiteEntryReplaceComma($entryText) .
                            ' ' . $entry['publisher'] . ',';
                }
            }
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' ' . $entry['year'] . ',' :
                    '';
            $entryText .= (array_key_exists('volume', $entry)) ?       // Volume
                    ' vol. ' . $entry['volume'] . ',' :
                    '';

            break;

        case "article":
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '&ldquo;' . $title . ',&rdquo;';       // Title
            $entryText .= (array_key_exists('journal', $entry)) ?      // Journal
                    ' <em>' . $entry['journal'] . '</em>,' :
                    '';
            $entryText .= (array_key_exists('volume', $entry)) ?       // Volume
                    ' vol. ' . $entry['volume'] . ',' :
                    '';
            $entryText .= (array_key_exists('number', $entry)) ?       // Number
                    ' no. ' . $entry['number'] . ',' :
                    '';
            $entryText .= (array_key_exists('pages', $entry)) ?        // Pages
                    ' pp. ' . str_replace(array('--', '-'), 
                                          array('&ndash;', '&ndash;'),
                                          $entry['pages']) . ',' :
                    '';
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' ' . $entry['year'] . ',' :
                    '';
            
            break;

        case "mastersthesis":
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '&ldquo;' . $title . ',&rdquo;';       // Title
            $entryText .= " Master's thesis, ";                        // Master's thesis
            $entryText .= (array_key_exists('school', $entry)) ?       // School
                    ' ' . $entry['school'] . ',' :
                    '';
            $entryText .= (array_key_exists('address', $entry)) ?       // School
                    ' ' . $entry['address'] . ',' :
                    '';
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' ' . $entry['year'] . ',' :
                    '';
            
            break;

        case "phdthesis":
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '&ldquo;' . $title . ',&rdquo;';       // Title
            $entryText .= " Ph.D. dissertation, ";                     // Ph.D. dissertation
            $entryText .= (array_key_exists('school', $entry)) ?       // School
                    ' ' . $entry['school'] . ',' :
                    '';
            $entryText .= (array_key_exists('address', $entry)) ?      // School
                    ' ' . $entry['address'] . ',' :
                    '';
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' ' . $entry['year'] . ',' :
                    '';

            break;
        
        case "electronic":

            $authors = BibtexCiteEntryReplaceComma($authors);          // Authors
            $entryText .= '<span class="BCAuthors">' . $authors .      
                '</span>';
            $entryText .= (array_key_exists('year', $entry)) ?         // Year
                    ' (' . $entry['year'] . ')' :
                    '';
            $entryText .= ' ' . $title . '.';                          // Title
            if (array_key_exists('url', $entry))
            {
                $url = $entry['url'];
                $entryText .= ' [Online]. Available: ' . '<a class="' . BIBTEX_LINK . '" href="' . $url . '">' . $url . '</a>';
            }
            
            break;
        
        // A minimal default format to roll back to in case all else fails
        default:
            
            $entryText .= '<span class="' . BIBTEX_AUTHORS . '">' . $authors .      // Authors
                '</span>';
            $entryText .= ' ' . '<em>' . $title . '</em>, ';
            $entryText .= (array_key_exists('year', $entry)) ?
                    ' ' . $entry['year'] . '.' :
                    '';
            break;
    }
    
    // Replace comma with period
    $entryText = BibtexCiteEntryReplaceComma($entryText);
    
    // Strip {, } characters
    $entryText = $latexSymbols->removeCurlyBraces($entryText);
    
    // Relace special symbols
    $entryText = $latexSymbols->replaceLatexChars($entryText);
    $entryText = $latexSymbols->replaceLatexSymbols($entryText);
    return $entryText;
    
}

// Format entry authors
function BibtexCiteEntryFormatAuthors($authorArray)
{
    $authorText = "";
    $authorCount = count($authorArray);

    for ($n=0; $n<$authorCount; $n++)
    {
        // Initials
        $initials  = "";
        $firstName = $authorArray[$n]['first'];
        
        // Get initials
        $names = explode(' ', $firstName);
        foreach ($names as $name)
        {
            $initials .= strtoupper(substr($name, 0, 1)) . '. ';
        }
        $initials = trim($initials);

        // Initials
        $authorText .= $initials . " ";
        
        // Prefix?
        $authorText .= empty($authorArray[$n]['von']) ? '' :
            ' ' . $authorArray[$n]['von'] . ' ';
        
        // Last name
        $authorText .= $authorArray[$n]['last'];
        
        // Postfix?
        $authorText .= empty($authorArray[$n]['jr']) ? '' : ', ' . $authorArray[$n]['jr'];
        
        // If more than one author, use "and" between two last authors
        if ($authorCount > 1)
        {
            if ($n == ($authorCount - 2))
            {
                $authorText .= ($authorCount > 2) ? ', ' : '';
                $authorText .= ' and ';
            } else
            {
                $authorText .= ', ';
            }
        } else
        // Otherwise separate by a comma
        {
            $authorText .= ', ';
        }  
    }
    
    return $authorText;
}

// Format entry title
function BibtexCiteEntryFormatTitle($entry)
{
    $title = $entry['title'];
    $titleText = '';
    
    // Add a link to PDF, if exists
    if (array_key_exists('pdflink', $entry) && !empty($entry['pdflink']))
    {
        if (empty($title))
        {
            $title = $entry['pdflink'];
        }
        
        $titleText .= '<a class="' . BIBTEX_PDF_LINK .'" href="' . $entry['pdflink'] . '">'. $title . '</a>';
    } else
    {
        $titleText .= $title;
    }
    
    if (!empty($titleText))
    {
        $titleText = '<span class="' . BIBTEX_TITLE . '">' . $titleText . '</span>';
    }
    
    return $titleText;
}

// Replace comma with period
function BibtexCiteEntryReplaceComma($text)
{
    $text = trim($text);
    if (substr($text, -1, 1) == ',')
    {
        $text = substr($text, 0, strlen($text)-1) . '.';
    }
    return $text;
}

// Get correct edition spelling
function BibtexCiteEntryReplaceEdition($edition)
{
    if (!in_array(($edition % 100),array(11,12,13))){
      switch ($edition % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $edition.'st';
        case 2:  return $edition.'nd';
        case 3:  return $edition.'rd';
      }
    }
    return $edition.'th';
}

?>

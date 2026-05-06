<?php

/**
 * --------------------------------------------------------------------------
 * LatexStructure :: section/subsection/subsubsection numbering, refs and TOC
 * --------------------------------------------------------------------------
 *
 * @author Aleksei Tepljakov
 * @version 0.3b
 */

// Need LatexUtils
require_once("LatexUtils.php");

class LatexStructure
{
    public function parseDocumentStructure($text)
    {
        // Set counters
        $counter = array('section' => 0,    // Numbered section counter
            'subsection' => 0, 
            'subsubsection' => 0);
        $ucounter = 1;                      // Unnumbered section counter (for TOC links)
        
        $refLabels = array();
        
        // Table of contents
        $toc = '<div class="' . LATEX_TOC_STYLE . '"><div class="' . 
                LATEX_TOC_TITLE_STYLE . '">' .
                LATEX_TOC_TITLE .
                '</div><ul>';

        // Get all major document structure items
        $this->getStructureItems($text, $result);
        for ($i = 0; $i < count($result[0]); $i++)
        {
            $heading = $result[0][$i];
            $headingType = $result[1][$i];
            $headingContent = $result[2][$i];
            
            // Get label and toc line if present
            $labels = $this->parseHeadingContent($headingContent);

            // Remove label{}/tocaddline{}
            $headingContent = $this->removeLabelsAddTocLines($headingContent);
            
            // Numbered entry
            if (substr($headingType, -1, 1) != '*')
            {
                // Depending on heading type, form numerical and increment counters
                switch($headingType)
                {
                    case 'section':
                        
                        $number = ++$counter['section'];
                        
                        $counter['subsection'] = 0;
                        $counter['subsubsection'] = 0;
                        
                        $tocTag = LATEX_SECTION;
                        $tocLevel = 1;
                        
                        break;
                    
                    case 'subsection':
                        
                        $number = $counter['section'] . '.' .
                            ++$counter['subsection'];
                        
                        $counter['subsubsection'] = 0;
                        
                        $tocTag = LATEX_SUBSECTION;
                        $tocLevel = 2;
                        
                        break;
                    
                    case 'subsubsection':
                        
                        $number = $counter['section'] . '.' .
                            $counter['subsection'] . '.' .
                            ++$counter['subsubsection'];
                        
                        $tocTag = LATEX_SUBSUBSECTION;
                        $tocLevel = 3;
                        
                        break;
                }
                
                if (!empty($labels['label']))
                {
                    // Add label, if present
                    $refLabels[] = array(
                    'label' => $labels['label'],
                    'reference' => $number);
                    
                    // Form heading
                    $headingLink = $labels['label'];
                }
                else
                {
                    // Use generic link
                    $headingLink = LATEX_TOC_STYLE . str_replace('.', '-', $number);
                }
                
                // Heading content according to \addtocline{}
                if (!empty($labels['addtocline']))
                {
                    $headingLinkContent = $labels['addtocline'];
                }
                else
                {
                    $headingLinkContent = $headingContent;
                }
                
                // Add TOC entry
                $toc .= '<li class="' . LATEX_TOC_LEVEL . $tocLevel . '">' . 
                        '<a href="#' . $headingLink . '">' . $number . '&nbsp;&nbsp;' . $headingLinkContent . '</a>'.
                        '</li>';
                
                $startHeadTag = '<' . $tocTag . ' class="' . LATEX_HEADING_STYLE .'" id="' . $headingLink . '">';
                $endHeadTag = '</' . $tocTag . '>';
                
                // Replace in-text heading
                $text = $this->strReplaceFirst($heading, 
                        $startHeadTag . (ADD_SECTION_NUMBER ? $number . '&nbsp;&nbsp;' : ' '). $headingContent . $endHeadTag,
                        $text);
                
            }
            // Unnumbered entry
            else
            {
                switch($headingType)
                {
                    case 'section*':
                        
                        $tocTag = LATEX_SECTION;
                        $tocLevel = 1;
                        
                        break;
                    
                    case 'subsection*':
                        
                        $tocTag = LATEX_SUBSECTION;
                        $tocLevel = 2;
                        
                        break;
                    
                    case 'subsubsection*':
                        
                        $tocTag = LATEX_SUBSUBSECTION;
                        $tocLevel = 3;
                        
                        break;
                }
                
                // Use generic link
                $headingLink = LATEX_TOC_STYLE . 'Unnumbered' . $ucounter++;
                
                // Heading content according to \addtocline{}
                if (!empty($labels['addtocline']))
                {
                    $toc .= '<li class="' . LATEX_TOC_LEVEL . $tocLevel . '">' .
                            '<a href="#' . $headingLink . '">' . $labels['addtocline'] . '</a>' .
                            '</li>';
                }
                
                $startHeadTag = '<' . $tocTag . ' class="' . LATEX_HEADING_STYLE .'" id="' . $headingLink . '">';
                $endHeadTag = '</' . $tocTag . '>';
                
                // Replace in-text heading
                $text = $this->strReplaceFirst($heading, 
                        $startHeadTag . $headingContent . $endHeadTag,
                        $text);
            }
            
        }
        
        $toc .= '</ul></div>';
        
        // Place table of contents
        $text = preg_replace('/\\\\tableofcontents(?:\{\})?/', $toc, $text);
        
        // Replace references in text
        foreach($refLabels as $label)
        {
            $text = str_replace('\\ref{' . $label['label'] . '}',
                    '<a class="' . LATEX_REFERENCE . '" href="#' . $label['label'] . '">' . $label['reference'] . '</a>',
                    $text);
        }
        
        // URL support
        $text = preg_replace('/\\\\url\{(\b(https?:\/\/|ftp:\/\/|file:\/\/|mailto:)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|])\}/i',
                             '<a href="\1">\1</a>',
                             $text);
        
        // HREF support
        $text = preg_replace('/\\\\href\{(\b(https?:\/\/|ftp:\/\/|file:\/\/|mailto:)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|])\}\{(.+?)\}/i',
                             '<a href="\1">\3</a>',
                             $text);
        
        return $text;
    }
    
    private function parseHeadingContent($text)
    {
        // Match a label
        $labelText = (preg_match('/\\\\label\{([A-Za-z0-9:_\-]+)\}/', $text, $regs)) ?
                $regs[1] :
                '';

        // Match a special \addtocline{} command
        $addTocLine = (preg_match('/\\\\addtocline\{(.+?)\}/', $text, $regs)) ?
                $regs[1] :
                '';
        return array('label' => $labelText, 'addtocline' => $addTocLine);
    }
    
    // Strip all captions/lables
    private function removeLabelsAddTocLines($text)
    {
        return preg_replace('/\\\\(?:label|addtocline)\{.*?\}/', '', $text);
    }
    
    // Replace first occurence only
    private function strReplaceFirst($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }
    
    private function getStructureItems($text, &$result)
    {
        // Resulting array is compatible
        // with the preg_match_all result
        $result = array();

        // Initialization of all internal arrays
        $result[0] = array();
        $result[1] = array();
        $result[2] = array();

        preg_match_all('/\\\\((?:sub)?(?:sub)?section\*?)\{/', $text, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
        for ($i=0; $i < count($matches[0]); $i++)
        {
            $cmdStart = $matches[0][$i][0];
            $cmdStartPos = $matches[0][$i][1];
            
            $k = $cmdStartPos + strlen($cmdStart);
            $leftCurlyCount = 1;

            while($k < strlen($text) && $leftCurlyCount > 0)
            {
                $leftCurlyCount += ($text[$k]=='{') ? 1 : 0;
                $leftCurlyCount += ($text[$k]=='}') ? -1 : 0;
                $k++;
            }
            
            $result[0][$i] = substr($text,
                    $cmdStartPos,
                    $k - $cmdStartPos);
            
            $result[1][$i] = $matches[1][$i][0];
            
            $result[2][$i] = substr($text,
                    $cmdStartPos+strlen($cmdStart),
                    $k-$cmdStartPos-strlen($cmdStart)-1);
        }
    }
}

?>

<?php

/**
 * LatexFloats :: A class to handle LaTeX floats and equations
 *
 * @author Aleksei Tepljakov
 * @version 0.1
 */

require_once 'LatexUtils.php';

class LatexFloats
{
    public function parseFloats($text)
    {
        $counter  = array('table' => 1, 'figure' => 1, 'equation' => 1);    // Counters
        $refLabels   = array();                                                // Labels
        
        preg_match_all('/(?s)\\\\begin\{([a-z]*?\*?)\}(.*?)\\\\end\{\1\}/', 
                $text, $result, PREG_PATTERN_ORDER);
        
        for ($i = 0; $i < count($result[0]); $i++)
        {
            $float  = $result[0][$i];
            $floatType = $result[1][$i];
            $floatContent = $result[2][$i];
            $floatText = '';
            
            // Depending on float type, perform necessary action
            switch($floatType)
            {
                // Table float
                case 'table':
                    
                    // Fetch label and caption, if available
                    $references = $this->parseFloatContent($floatContent);
                    
                    // Centering
                    $doCenter = $this->floatCentering($floatContent);
                    $floatContent = $this->removeFloatCentering($floatContent);
                    
                    if (!empty($references['caption']))
                    {
                        // Add label to label index
                        if (!empty($references['label']))
                        {
                            $refLabels[] = array(
                                'label' => $references['label'], 
                                'labeltype' => LATEX_FLOAT_TABLE,
                                'reference' => $counter[$floatType]);
                            $link = $references['label'];
                        } else
                        {
                            $link = LATEX_FLOAT_TABLE . $counter['table'];
                        }
                        
                        // Form float body
                        $floatText .= '<div id="' . $link .
                                '" class="' . $doCenter . FLOAT_PREFIX . ' ' . LATEX_FLOAT_TABLE . '">';
                        $floatText .= '<div class="' . LATEX_FLOAT_TABLE_CAPTION . '">';
                        $floatText .= str_replace('#', $counter[$floatType]++, TABLE_NAME) . $references['caption'];
                        $floatText .= '</div>';
                        $floatText .= $floatContent;
                        $floatText .= '</div>';
                    }
                    else
                    {
                        // Discard reference and label, if they exist in source text
                        $text = $this->removeRefLabel($text, $references['label']);
                        $floatText .= '<div class="' . $doCenter . FLOAT_PREFIX . ' ' . LATEX_FLOAT_TABLE . '">';
                        $floatText .= $floatContent;
                        $floatText .= '</div>';
                    }
                    
                    // Replace table
                    $text = $this->strReplaceFirst($float, $this->removeCaptionsLabels($floatText), $text);
                    
                    break;
                
                // Figure float
                case 'figure':
                    
                    // Fetch label and caption, if available
                    $references = $this->parseFloatContent($floatContent);
                    
                    // Centering
                    $doCenter = $this->floatCentering($floatContent);
                    $floatContent = $this->removeFloatCentering($floatContent);
                    
                    if (!empty($references['caption']))
                    {
                        // Add label to label index
                        if (!empty($references['label']))
                        {
                            $refLabels[] = array(
                                'label' => $references['label'],
                                'labeltype' => LATEX_FLOAT_FIGURE,
                                'reference' => $counter[$floatType]);
                            $link = $references['label'];
                        } else
                        {
                            $link = LATEX_FLOAT_FIGURE . $counter['figure'];
                        }
                        
                        // Form float body
                        $floatText .= '<div id="' . $link .
                                '" class="' . $doCenter . FLOAT_PREFIX . ' ' . LATEX_FLOAT_FIGURE . '">';
                        $floatText .= $floatContent;
                        $floatText .= '<div class="' . LATEX_FLOAT_FIGURE_CAPTION . '">';
                        $floatText .= str_replace('#', $counter[$floatType]++, FIGURE_NAME) . $references['caption'];
                        $floatText .= '</div>';
                        $floatText .= '</div>';
                    }
                    else
                    {
                        // Discard reference and label, if they exist in source text
                        $text = $this->removeRefLabel($text, $references['label']);
                        $floatText .= '<div class="' . $doCenter . FLOAT_PREFIX . ' ' . LATEX_FLOAT_FIGURE . '">';
                        $floatText .= $floatContent;
                        $floatText .= '</div>';
                    }
                    
                    // Replace figure
                    $text = $this->strReplaceFirst($float, 
                            $this->removeCaptionsLabels($floatText),
                            $text);
                    
                    break;
                
                // Numbered equation
                case 'equation':
                case 'align':
                case 'eqnarray':
                case 'multline':

                    // Fetch label and caption, if available
                    $references = $this->parseFloatContent($floatContent);

                    // Add label to label index
                    if (!empty($references['label']))
                    {
                        $refLabels[] = array(
                            'label' => $references['label'],
                            'labeltype' => LATEX_EQUATION,
                            'reference' => $counter['equation']);
                        $link = $references['label'];
                    } else
                    {
                        $link = LATEX_EQUATION . $counter['equation'];
                    }

                    // Form float body
                    $floatText .= '<div id="'. $link .
                            '" class="' . FLOAT_PREFIX . ' ' . LATEX_EQUATION . '">';
                    $floatText .= '\\begin{'  . $floatType .  '}' . $floatContent . 
                            '\\tag{' . $counter['equation'] . '}' . '\\end{' . $floatType .  '}';
                    $floatText .= '</div>';
                    
                    // Increment counter
                    $counter['equation']++;

                    // Replace equation
                    $text = $this->strReplaceFirst($float, 
                            $this->removeCaptionsLabels($floatText),
                            $text);

                    break;
                
                // Un-numbered equation
                case 'equation*':
                case 'align*':
                case 'eqnarray*':
                case 'multline*':    
                    
                    // Form float body
                    $floatText .= '<div class="' . FLOAT_PREFIX . ' ' . LATEX_EQUATION . '">';
                    $floatText .= '\\begin{' . $floatType . '}' . $floatContent . '\\end{'  . $floatType .  '}';
                    $floatText .= '</div>';
                    
                    // Replace unnumbered equation
                    $text = $this->strReplaceFirst($float, 
                            $this->removeCaptionsLabels($floatText),
                            $text);
                    
                    break;
            }
        }
        
        // Replace references in text
        foreach($refLabels as $label)
        {
            $text = str_replace('\\ref{' . $label['label'] . '}',
                    '<a class="' . LATEX_REFERENCE . '" href="#' . $label['label'] . '">' . $label['reference'] . '</a>',
                    $text);
        }
        
        // Replace equation references in text
        foreach($refLabels as $label)
        {
            $text = str_replace('\\eqref{' . $label['label'] . '}',
                    '(<a class="' . LATEX_REFERENCE . '" href="#' . $label['label'] . '">' . $label['reference'] . '</a>)',
                    $text);
        }

        return $text;
    }
 
    // Match label/caption in a block of text and return them as a structured array
    private function parseFloatContent($text)
    {
        // Match a label
        $labelText = (preg_match('/\\\\label\{([A-Za-z0-9:_\-]+)\}/', $text, $regs)) ?
                $regs[1] :
                '';
        
        // Match a caption
        $captionText = '';
        $captionTextArray = LatexUtils::delimitedMatch("{}", $text, "\\caption");
        if ($captionTextArray)
        {
            $captionText = $captionTextArray[0]["expression"];
        }

        return array('label' => $labelText, 'caption' => $captionText);
    }
    
    // Replace in-text occurences of \ref{} and \label{} for given reference
    private function removeRefLabel($text, $reference)
    {
        $text = str_replace('\\ref{' . $reference . '}', '', $text);
        $text = str_replace('\\label{' . $reference . '}', '', $text);
        return $text;
    }
    
    // Strip all captions/lables
    private function removeCaptionsLabels($text)
    {
        // Remove labels
        $text = preg_replace('/\\\\(?:label)\{.*?\}/', '', $text);
        
        // Remove captions
        $text = str_replace(LatexUtils::delimitedMatch("{}", $text, "\\caption", DELIM_OUTPUT_FULL_MATCHES_ONLY), "", $text);
        
        return $text;
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
    
    // Check if content centering is requested
    private function floatCentering($text)
    {
        // Match a centering statement
        return (preg_match('/\\\\centering/', $text, $regs)) ?
                ' ' . LATEX_FLOAT_CENTERING . ' ':
                '';
    }
    
    // Remove centering statements from text
    private function removeFloatCentering($text)
    {
        return preg_replace('/\\\\centering(?:[\s{}]+)?/', '', $text);
    }
}

?>

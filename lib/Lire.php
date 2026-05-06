<?php

/**
 * -----------------------------------------------------------------------
 * Lire :: the base LiRE library class for parsing LaTeX-compatible syntax
 * -----------------------------------------------------------------------
 *
 * @author Aleksei Tepljakov
 * @version 0.1
 */

// Include configuration
require_once('LireConfig.php');

// Include child classes

require_once('LatexUtils.php');                   // Helper utilities used
                                                  // by several parsers

require_once('LatexSymbols.php');                 // Special characters and
                                                  // symbol conversion

require_once(ROOTDIR . 'bibtex' .                 // Bibtex citations
        DIRECTORY_SEPARATOR . 'BibtexCite.php');

require_once('LatexFloats.php');                  // Floating structures
                                                  // (featuring equations)

require_once('LatexStructure.php');               // Document structure
require_once('LatexFootnotes.php');               // Footnotes
require_once('LatexSidenotes.php');               // Sidenotes (Tufte style)


class Lire
{
    public function parseLatex($text)
    {
        // Create parsers and parse given text in correct order
        
        // Pre-parse replacement of \ in <pre> and <code>
        $ltSymbolConv = new LatexSymbols();
        $text = $ltSymbolConv->precodeTextBackslash($text);
        
        // Citations
        $ltBibtex = new BibtexCite();
        $text = $ltBibtex->parseText($text);
        
        // Float structure/equation references
        $ltFloats = new LatexFloats();
        $text = $ltFloats->parseFloats($text);
        
        // Document structure
        $ltStruct = new LatexStructure();
        $text = $ltStruct->parseDocumentStructure($text);
        
        // Footnotes
        $ltFootnotes = new LatexFootnotes();
        $text = $ltFootnotes->parseFootnotes($text);
		
		// Sidenotes
        $ltSidenotes = new LatexSidenotes();
        $text = $ltSidenotes->parseSidenotes($text);
        
        // Replace special symbols in-text
        $text = $ltSymbolConv->replaceTextBackslash($text);
        
        // Return parsed text
        return $text;
    }
}

?>

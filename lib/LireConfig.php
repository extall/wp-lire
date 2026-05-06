<?php

/**
 * LiRE library configuration file
 * 
 * @author Alexei Teplyakov
 * @version 0.1
 * 
 */

/*********
 * PATHS *
 *********/

// Root directory
define('ROOTDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/***********************
 * PREFIXES AND STYLES *
 ***********************/

// ----------------------------------
// Document structure and style class
// ----------------------------------
define('LATEX_SECTION', 'h2');           // (Sub)(sub)section tag mapping
define('LATEX_SUBSECTION', 'h3');
define('LATEX_SUBSUBSECTION', 'h4');

define('ADD_SECTION_NUMBER', false);

define('LATEX_TOC_STYLE', 'LatexTableOfContents');
define('LATEX_TOC_TITLE_STYLE', 'LatexTableOfContentsTitle');
define('LATEX_TOC_LEVEL', LATEX_TOC_STYLE . 'Level');

define('LATEX_TOC_TITLE', 'Table of Contents');

define('LATEX_HEADING_STYLE', 'LatexHeadingStyle');

// -----------------------
// Reference style classes
// -----------------------
define('LATEX_REFERENCE', 'LatexReference');

// --------------------------------
// Float prefixes and style classes
// --------------------------------
define('FLOAT_PREFIX', 'LatexFloat');

define('LATEX_FLOAT_FIGURE', FLOAT_PREFIX . 'Figure');
define('LATEX_FLOAT_TABLE', FLOAT_PREFIX . 'Table');
define('LATEX_EQUATION', FLOAT_PREFIX . 'Equation');

define('LATEX_FLOAT_CENTERING', FLOAT_PREFIX . 'Centering');

define('LATEX_FLOAT_FIGURE_CAPTION', FLOAT_PREFIX . 'FigureCaption');
define('LATEX_FLOAT_TABLE_CAPTION', FLOAT_PREFIX . 'TableCaption');

// ------------------------------
// Figure and Table caption names
// ------------------------------
define('FIGURE_NAME', 'Figure #. ');
define('TABLE_NAME', 'Table #. ');

// ---------------------------------
// Footnote format and style classes
// ---------------------------------
define('FOOTNOTE_REF_STYLE', 'LatexFootnote');
define('SIDENOTE_REF_STYLE', 'LatexSidenote');
define('FOOTNOTE_REF_FORMAT', '<sup class="' . FOOTNOTE_REF_STYLE .'">#</sup>');
define('SIDENOTE_REF_FORMAT', '<sup class="' . SIDENOTE_REF_STYLE .'">#</sup>');

// -----------------------------------
// Bibtex bibliography default formats
// -----------------------------------
define('BIBTEX_DEFAULT_BIBLIOGRAPHY_STYLE', 'IEEEtran');
define('BIBTEX_DEFAULT_CITATION_STYLE', 'plain');

// ----------------------------------------------
// Bibtex bibliography prefixes and style classes
// ----------------------------------------------
define('BIBTEX_PREFIX', 'BibtexCite');
define('BIBTEX_BIBLIOGRAPHY', BIBTEX_PREFIX . 'Bibliography');
define('BIBTEX_REFERENCE', BIBTEX_PREFIX . 'Reference');
define('BIBTEX_AUTHORS', BIBTEX_PREFIX . 'Authors');
define('BIBTEX_TITLE', BIBTEX_PREFIX . 'Title');
define('BIBTEX_LINK', BIBTEX_PREFIX . 'Link');
define('BIBTEX_PDF_LINK', BIBTEX_PREFIX . 'PdfLink');

?>

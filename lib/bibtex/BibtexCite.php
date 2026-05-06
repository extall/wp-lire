<?php

/**
 * ---------------------------------------------------------------------------
 * BibtexCite :: a class for including nicely formatted citations in user text
 * ---------------------------------------------------------------------------
 *
 * @author Alexei Teplyakov
 * @version 0.1
 */
// Class is based on the modified PEAR BibTeX parser
require_once ROOTDIR . 'bibtex/include/BibTex.php';

class BibtexCite
{

    // Public options
    public $bibtexFile;        // BIB bibliography location
    public $bibtexBib;         // Parsed BIB bibliography file
    public $bibtexOptions;     // Bibtex options, such as citation styles

    // Constructor

    public function __construct()
    {
        $this->bibtexFile = '';
        $this->bibtexBib = '';
        $this->bibtexOptions = array(
            "citationStyle" => BIBTEX_DEFAULT_CITATION_STYLE
        );
    }

    // Reference/citation parser
    public function parseText($text)
    {
        // Do preliminary actions
        // Seek bibliography style
        if (preg_match('/\\\\bibliographystyle\{(.+?)\}/', $text, $regs))
        {
            // Remove control characters for security; use the captured style name
            $bibstyle = preg_replace('/[\x00-\x1F\x7F]/', '', $regs[1]);
        } else
        {
            $bibstyle = BIBTEX_DEFAULT_BIBLIOGRAPHY_STYLE;
        }

        // Get path to bibliography style
        $bibStylePath = dirname(__FILE__) .
                DIRECTORY_SEPARATOR . 'bibstyle' . DIRECTORY_SEPARATOR .
                $bibstyle . '.php';

        // Check path
        if (!file_exists($bibStylePath))
        {
            $bibStylePath = dirname(__FILE__) .
                    DIRECTORY_SEPARATOR . 'bibstyle' . DIRECTORY_SEPARATOR .
                    BIBTEX_DEFAULT_BIBLIOGRAPHY_STYLE . '.php';
        }

        // Load specified bibliography style
        // It must contain a function with prototype
        // $text = BibtexCiteEntryFormat($entry), where $entry is
        // in BibTex format defined by Structures_BibTex
        require_once($bibStylePath);

        // Citation style
        $citeStyle = preg_replace('/[\x00-\x1F\x7F]/', '', $this->bibtexOptions['citationStyle']);

        $citeStylePath = dirname(__FILE__) .
                DIRECTORY_SEPARATOR . 'citestyle' . DIRECTORY_SEPARATOR .
                $citeStyle . '.php';

        if (!file_exists($citeStylePath))
        {
            $citeStylePath = dirname(__FILE__) .
                    DIRECTORY_SEPARATOR . 'citestyle' . DIRECTORY_SEPARATOR .
                    BIBTEX_DEFAULT_CITATION_STYLE . '.php';
        }

        // Load specified citation style
        // It must contain functions with prototype
        // $text = BibtexCiteCitationFormat($keys, $citationIndex, $bibtexBib),
        // $text = BibtexCiteCitationPlainFormat($keys, $citationIndex, $bibtexBib),
        // where keys should be separated by commas, e.g. Cite1,Cite2,Cite3
        require_once($citeStylePath);

        // Get BIB file(s)
        preg_match_all('/\\\\bibliography\{(.+?)\}/', $text, $regs, PREG_SET_ORDER);

        // Global citation index
        $globalCitationIndex = array();
        $globalBibliography = array();
        
        foreach ($regs as $reg_num => $reg)
        {
            $bibfile = $reg[1];

            // Check whether BIB extension is present
            $bibfile_check = explode('.', $bibfile);
            if (strtolower($bibfile_check[count($bibfile_check) - 1]) !== 'bib')
            {
                $bibfile .= '.bib';
            }

            if (!empty($bibfile))
            {
                // Load BIB file
                $this->loadBibFile($bibfile);
                
                // Add to bibliography to global bibliography
                $globalBibliography = array_merge($globalBibliography, $this->bibtexBib);

                // Find all citations within the text
                $citationIndex = array();
                $citationNumber = 1;

                preg_match_all('/\\\\cite\{(.+?)\}/', $text, $result, PREG_PATTERN_ORDER);
                for ($i = 0; $i < count($result[1]); $i++)
                {
                    // Build index
                    $keys = explode(',', $result[1][$i]);
                    foreach ($keys as $key)
                    {
                        $key = trim($key); // Remove white space
                        if (!array_key_exists($key, $citationIndex) &&
                                array_key_exists($key, $this->bibtexBib))
                        {
                            $globalCitationIndex[$key] = $citationNumber;
                            $citationIndex[$key] = $citationNumber++;
                        }
                    }
                }

                // If \nocite{*} present, fill the rest of the index with all
                // bibliography entries in the same order as they appear in the
                // bib file
                $ncPos = strpos($text, "\\nocite{*}");
                if ($ncPos !== false)
                {
                    foreach ($this->bibtexBib as $entryKey => $bibtexEntry)
                    {
                        // Add if not already indexed
                        if (!array_key_exists($entryKey, $citationIndex))
                        {
                            $globalCitationIndex[$entryKey] = $citationNumber;
                            $citationIndex[$entryKey] = $citationNumber++;
                        }
                    }
                }

                // Build bibliography
                $bibliography = "<table class='" . BIBTEX_BIBLIOGRAPHY . "'>";
                foreach ($citationIndex as $key => $number)
                {
                    $bibliography .= "<tr>";
                    $bibliography .= "<td><a class='" . BIBTEX_REFERENCE . "' id='" . BIBTEX_PREFIX .
                            "$key' name='" . BIBTEX_PREFIX . "$key'>" .
                            BibtexCiteCitationPlainFormat($key, $citationIndex, $this->bibtexBib) .
                            "</a></td>";
                    $bibliography .= "<td>" . BibtexCiteEntryFormat($this->bibtexBib[$key]) . "</td>";
                    $bibliography .= "</tr>";
                }
                $bibliography .= "</table>";

                // Replace bibliography and bibliographystyle in text
                $text = preg_replace('/\\\\bibliography\{(.+?)\}/', $bibliography, $text, 1);
            }
        }
        
        // Replace citations in text
        preg_match_all('/\\\\cite\{(.+?)\}/', $text, $result, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($result[1]); $i++)
        {
            $keys = $result[1][$i];
            $citation = BibtexCiteCitationFormat($keys, $globalCitationIndex, $globalBibliography);
            $text = str_replace($result[0][$i], $citation, $text);
        }

        // Clean up
        $text = preg_replace('/\\\\bibliographystyle\{(.+?)\}/', '', $text);
        $text = str_replace("\\nocite{*}", '', $text);

        // Return formatted text
        return $text;
    }

    // BIB file loader
    private function loadBibFile($file)
    {
        // TODO: add "file not found" error handling
        $this->bibtexFile = $file;
        $btParser = new Structures_BibTex;
        $btParser->loadFile($file);
        $btParser->parse();

        // TODO: OPTIMIZE multiple bibliographies handling
        
        // Create empty array
        $this->bibtexBib = array();
        
        // Re-format bibliography array to cite() => array()
        foreach ($btParser->data as $entry)
        {
            if (array_key_exists('cite', $entry) &&
                    $btParser->checkAllowedEntryType($entry['entryType']))
            {
                $this->bibtexBib[$entry['cite']] = $entry;
            }
        }
    }

}

?>

<?php

/*
  Plugin Name: LiRE for WordPress
  Plugin URI: http://starspirals.net/lire/
  Description: A WordPress plugin for LaTeX-compatible document parsing with LiRE (LaTeX inspired Reference Extensions)
  Author: Aleksei Tepljakov
  Version: 0.3b
  Author URI: http://starspirals.net/

  Copyright (C) 2011-2016 Aleksei Tepljakov

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3
  of the License, or any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("WPLire"))
{

    class WPLire
    {
        public $pluginURL;
        public $pluginPath;

        public function __construct()
        {
            $this->pluginURL = WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__));
            $this->pluginPath = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
        }

        public function addMathConfig()
        {
            $configFile = $this->pluginPath . "/js/Math.js";
            if (file_exists($configFile))
            {
                $mathConfig = file_get_contents($configFile);
                echo '<script type="text/x-mathjax-config">';
                echo $mathConfig;
                echo '</script>';
            }
        }

        public function enqueueAssets()
        {
            $lireStylePath = $this->pluginPath . "/lib/css/default.css";
            $lireStyleUrl = $this->pluginURL . "/lib/css/default.css";
            if (file_exists($lireStylePath))
            {
                wp_register_style('WpLireStyle', $lireStyleUrl);
                wp_enqueue_style('WpLireStyle');
            }

            // Math script (cdn.mathjax.org was retired; use cdnjs)
            wp_register_script('WpLireMathJax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.9/MathJax.js?config=TeX-AMS_HTML');
            wp_enqueue_script('WpLireMathJax');
        }

        // Keep the parser nice and tidy for now
        public function parseDocument($content)
        {
            require_once($this->pluginPath . '/lib/Lire.php');
            $lire = new Lire();
            return $lire->parseLatex($content);
        }
    }
}

if (class_exists("WPLire"))
{
    $wpLire = new WPLire();

    // Enqueue styles/scripts at the proper hook
    add_action('wp_enqueue_scripts', array($wpLire, 'enqueueAssets'));

    // Add math configuration script
    add_action('wp_head', array($wpLire, 'addMathConfig'), 1);

    // Parse LaTeX in document content (the_content is a filter)
    add_filter('the_content', array($wpLire, 'parseDocument'), 2);
}

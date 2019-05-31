<?php
/*
Plugin Name: RDF exposer
Plugin URI: tainacan.org
Description: Plugin for exporser tainacan collections as RDF (JSON-LD)
Author: Media Lab / UFG
Version: 0.0.1
Text Domain: tainacan-rdf-exposer
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

require_once plugin_dir_path(__FILE__) . 'classes/custom-form-rdf.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-tainacan-exposer_rdf.php';


$customFormRDF = new CustomFormRDF();
<?php
namespace Tainacan\RDF;

add_action('init', function( ) {
	
	class ExposerRDF extends \Tainacan\Exposers\Exposer {

		public $slug = 'exposer-rdf';
		public $mappers = true;
		public $accept_no_mapper = true;

		function __construct() {
			
			$this->set_name( __('JSON-LD') );
			$this->set_description( __('Json for Linked Data', 'rdf-exposer') );
		}

		protected $contexts = [];

		/**
		 * 
		 * {@inheritDoc}
		 * @see \Tainacan\Exposers\Types\Type::rest_request_after_callbacks()
		 */
		public function rest_request_after_callbacks( $response, $handler, $request ) {
			$response->set_headers([
				'Content-Type: application/json; charset=' . get_option( 'blog_charset' ),
				'Link: <'.get_bloginfo('url').'/item.jsonld>; rel="http://www.w3.org/ns/json-ld#context"; type="application/ld+json"'
			]);
			
			$mapper = \Tainacan\Mappers_Handler::get_instance()->get_mapper_from_request($request);
			if($mapper && property_exists($mapper, 'XML_namespace') && !empty($mapper->XML_namespace)) {
				foreach ($mapper->prefixes as $prefix => $schema) {
					$this->contexts[$prefix] = $schema;
				}
				
				foreach ($response->get_data()['items'] as $item) {
					foreach ($item['metadata'] as $meta_id => $meta_value) {
						if ( !empty($meta_value['mapping']) ) {
							foreach($meta_value['mapping'] as $map => $map_value) {
								$this->contexts[$meta_value['name']] = ["@id" => $map_value]; //pode ter mais de um mapper usar o mapp passado pela URL?
							}
						}
					}
				}
			} else {
				foreach ($response->get_data()['items'] as $item) {
					foreach ($item['metadata'] as $meta_id => $meta_value) {
						$this->contexts[$meta_value['name']] = $meta_value['semantic_uri'];
					}
				}
			}
			$this->contexts['@language'] = $this->get_locale($response->get_data()['items']);

			$contexts =  '"@context":' . \json_encode($this->contexts);
			$jsonld = $this->array_to_jsonld($contexts, $response->get_data()['items']);
			$response->set_data("[" . $jsonld . "]");
			return $response;
		}

		protected function array_to_jsonld( $contexts, $data) {
			$jsonld = '';
			$item_jsonld = [];
			foreach ($data as $item) {
				$metadatas = [];
				foreach ($item['metadata'] as $metadata) {
					$metadatas[] = '"' . $metadata["name"] . '":' . \json_encode($metadata["value"]);
				}
				$item_jsonld[] = '{' . $contexts . ',' . \implode(",", $metadatas) . '}';
			}
			return \implode(",", $item_jsonld);
		}
	
		public function get_locale($obj) {
				if(array_key_exists('ID', $obj) && function_exists('wpml_get_language_information')) {
						$lang_envs = wpml_get_language_information($obj['ID']);
						return $lang_envs['locale'];
				}
				return get_locale();
		}
	}

	$exposers = \Tainacan\Exposers_Handler::get_instance();
	$exposers->register_exposer('Tainacan\RDF\ExposerRDF');
});


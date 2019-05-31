<?php
class CustomFormRDF {

	private $field_predicado = 'rdf-predicado-metadata';

	function __construct() {
		add_action( 'tainacan-register-admin-hooks',				[$this, 'registerHook'] );
		add_action( 'tainacan-insert-tainacan-metadatum',		[$this, 'saveMeta'] );
		add_action( 'tainacan-insert-tainacan-collection',	[$this, 'saveMeta'] );
		add_filter( 'tainacan-api-response-metadatum-meta', [$this, 'addMetaToResponse'], 10, 2 );
		//add_filter( 'tainacan-api-response-collection-meta', [$this, 'addMetaToResponse'], 10, 1 );
	}

	function registerHook() {
		if ( function_exists( 'tainacan_register_admin_hook' ) ) {
			tainacan_register_admin_hook( 'metadatum', array( $this, 'addCompomentOnForm' ) );
			tainacan_register_admin_hook( 'collection', array( $this, 'addCompomentOnForm' ), 'end-right' );
		}
	}

	function saveMeta( $object ) {
		if ( ! function_exists( 'tainacan_get_api_postdata' ) ) {
			return;
		}
		$post = tainacan_get_api_postdata();
		if ( $object->can_edit() ) {
			if ( isset( $post->{$this->field_predicado} ) ) {
				update_post_meta( $object->get_id(), $this->field_predicado, $post->{$this->field_predicado} );
			}
		}
	}

	function addMetaToResponse( $extra_meta, $request ) {
		$extra_meta = array_merge($extra_meta, [$this->field_predicado] );
		return $extra_meta;
	}

	function addCompomentOnForm() {
		if ( ! function_exists( 'tainacan_get_api_postdata' ) ) {
				return '';
		}
		ob_start();
		?>
			<div class="field tainacan-term-show-link">
				<label class="label"><?php _e( 'Predicado', 'tainacan-interface' ); ?></label>
				<span class="help-wrapper">
					<a class="help-button has-text-secondary">
						<span class="icon is-small">
							<i class="mdi mdi-help-circle-outline"></i>
						</span>
					</a>
					<div class="help-tooltip">
						<div class="help-tooltip-header">
							<h5><?php _e( 'Predicado', 'tainacan-interface' ); ?></h5>
						</div>
						<div class="help-tooltip-body">
							<p><?php _e( 'Predicado', 'tainacan-interface' ); ?></p>
						</div>
					</div>
				</span>

				<div class="control">
					<input type="text" autocomplete="on" name="<?php echo $this->field_predicado; ?>" id="<?php echo $this->field_predicado; ?>" class="input">
				</div>
			</div>
		<?php
		return ob_get_clean();
	}
}
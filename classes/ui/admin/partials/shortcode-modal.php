<?php
/**
 * HTML markup for shortcode inserter
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
?>
<style>

	.ingot-modal-button{
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-ms-touch-action: manipulation;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-image: none;
		border: 1px solid rgba(0, 0, 0, 0);
		border-radius: 4px;
		color: #fff;
		background-color: #72386A;
		border-color: #6c7761;
	}
	.ingot-modal-button a{
		color: #fff;
		text-decoration: initial;
	}
	.ingot-modal-button:hover{
		background-color: #A8CE75;
		border-color: white;
	}
	#ingot-insert-shortcode-button{
		float:right;
		background-color: #A8CE75;
		border-color: #6c7761;
	}
	#ingot-insert-shortcode-button:hover{
		background-color: #72386A;
	}

	#ingotShortcodeModal_calderaModalBody {
		background-color: #fff;
	}
	#ingotShortcodeModal_calderaModalTitle{
		background-color: #72386A;
	}

	h3#ingotShortcodeModal_calderaModalLable {
		color: #FFF;
	}

	#ingotShortcodeModal_calderaModalFooter{
		background-color: #fff;
		border-top: 1px solid #6c7761;
	}
	.caldera-modal-closer{
		color:#A8CE75;
	}

	.caldera-modal-closer:hover{
		color:#fff;
	}

</style>
//main modal content
?>
<script type="text/html" id="ingot-post-modal">
	<?php if ( empty( $groups ) ) {
		echo '<p>';
		esc_html_e( 'No Ingot Content Tests Exist', 'ingot' );
		echo '</p>';
		printf( '<p>%s</p>', \ingot\ui\admin::admin_url_html( __( 'Perhaps you would like to create one?', 'ingot' ) ) );
	} else {
		echo '<form id="ingot-group-chooser">';
		foreach ( $groups as $group ) {
			printf( '<p><input type="radio" name="ingot-group" value="%d">%s</p>', $group[ 'ID' ], $group[ 'name' ] );
		}
		echo '</form>';
	} ?>
</script>
<?php //modal footer ?>
<script type="text/html" id="ingot-post-modal-footer">
	<?php
		printf( '<button class="ingot-modal-button">%s</button>', \ingot\ui\admin::admin_url_html( __( 'Go To Ingot Admin', 'ingot' ) ) );
		printf( '<button id="ingot-insert-shortcode-button" class="ingot-modal-button" style="visibility: hidden" aria-hidden="true">%s</button>', esc_html__( 'Insert Shortcode' ) );
	?>

</script>

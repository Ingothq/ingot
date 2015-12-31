<?php
namespace ingot\ui\admin;


class ingot_metabox {

	public static function box_view( $post ) {

		echo '<div class="container" ng-app="ingotMetaApp">
				<div ng-controller="metaController" ng-show="loaded" class="group-panel-wrapper">
					<h2>Content Tests</h2>
					<article ng-repeat="group in groups" class="group-panel">
						<h5>{{ group.name }}</h5>
						<div>
							Shortcode: <code>[ingot id="{{group.ID}}"]</code>
						</div>
					</article>
				</div>
			</div>';

	}

}
?>
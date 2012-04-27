<?php
/*!
 * @desc Render a view template.
 * @author Shawn Melton <shawn.melton@webteks.com>
 * @verison $id: $
 */
class View extends BaseObject {
	public function render($file) {
		ob_start();
		require_once dirname(dirname(__FILE__)) .'/views/'. $file;
		return ob_get_clean();	
	}
}
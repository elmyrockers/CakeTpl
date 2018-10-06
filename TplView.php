<?php

/**
 * This file is part of CakeTpl.
 *
 ** (c) 2018 Mohd Helmi Aziz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace elmyrockers\CakeTpl;

use Cake\View\View;
use Cake\Core\Configure;






/**
 * 
 */
class TplView extends View
{
	protected $_ext = '.tpl';
	protected function _evaluate($viewFile, $dataForView)
	{
		// pr( $viewFile );
		// pr( $dataForView );
		$generatedFile = $viewFile;

		# TplParser hanya akan digunakan apabila extension ditetapkan kepada '.tpl'
		# DEVELOPMENT MODE: Proses menterjemah dilakukan setiap kali 'reload'
		# PRODUCTION MODE: 	Proses menterjemah dilakukan hanya sekali
		# 					( Iaitu hanya jika fail yang dijana belum wujud / telah dipadam ) 
			if ( $this->_ext == '.tpl' ) {
				$developmentMode = Configure::read( 'debug' );
				$generatedFile = str_replace( APP.'Template', TMP.'tpl', $viewFile );

				if ( $developmentMode || !file_exists( $generatedFile ) ) {
					(new TplParser($viewFile))->save( $generatedFile );
				}
			}

		# Hasilkan paparan output
			extract($dataForView);
			ob_start();

			include $generatedFile;

			return ob_get_clean();
	}
}
<?php
/**
 * This file is part of CakeTpl.
 *
 ** (c) 2018 Helmi Aziz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
**************************************************************************
 							DOCUMENTATION
**************************************************************************
1. Tag PHP
	- {%  %}
	- {{  }}
	- Pastikan tiada tanda tag sebegini dalam kod PHP
2. Komen
	- Multi-line   |* Komen *|
	- Single-line  |- Komen
3. Literal
	- {literal}{/literal}
	- {l}{/l}
4. Memaparkan Data (echo)
	- {$var}
	- {'data anda'}
	- {fetch('content')}
4. Struktur Kawalan
	- Pernyataan Bersyarat
		- {if $expression}
		- {elseif $expression}
		- {else}
		- {/if}

		- {switch $var}
		- {case 'Arnab'}
		- {default}
		- {/switch}
	- Pernyataan Berulangan
		- {foreach $array as $key => $nilai}
		- {/foreach}

		- {while $var}
		- {/while}

		- {for $a=0; $a<=10; $a++}
		- {/for}

		- {break}
		- {continue}
	- Pastikan setiap tanda { ditutup dengan }, nested dibenarkan
5. Senarai Function - CakePHP
	- Template
		- extend()
		- assign()
		- start()
		- end()
		- append()
		- prepend()
		- reset()

		- fetch()
		- element()
		- cell()
		- cache()

		-{extend ''}
		-{assign '',''}
		-{start ''}
		-{end}
		-{append ''}
		-{prepend ''}
		-{reset ''}

		-{fetch ''}
		-{element ''}
		-{cell ''}
		-{cache ''}
	- Helper
		- Form Helper
			- form_create()
			- form_end()

			- label()
			- text()
			- email()
			- password()
			- textarea()
			- select()
			- radio()
			- checkbox()
			- file()
			- hidden()
			- button()

			- form_error()
		- HTML Helper
			- meta()
			- css()
			- js()
			- img()
			- a()
			- nestedList()
			- tableHeaders()
			- tableCells()

			- cssV()
			- jsV()
			- imgV()
		- Helper: Umum
			$request

			$Breadcrumbs
			$Flash
			$Form
			$Html
			$Number
			$Paginator
			$Rss
			$Text
			$Time
			$Url
	- Kesemua function boleh digunakan di mana-mana samada di dalam PHP TAG, ECHO ataupun STRUKTUR KAWALAN
****************************************************************************************************************/
declare( strict_types = 1 );
namespace elmyrockers\CakeTpl;







function p( $value )
{
	print_r( $value );
}

function v( $value )
{
	var_dump( $value );
}







class TplParser
{
	private $_content = '';
	private $_literals = [];
	private $_phpModes = [];
	private $_tags = [];

	public function __construct( string $tpl = NULL, bool $isContent = FALSE )
	{
		if ( !$tpl ) {
			throw new \Exception("Tpl path or content is empty", 1);
		}

		if ( !$isContent && !file_exists( $tpl ) ) {
			throw new \Exception("File is not exists", 1);
		}

		# Tetapkan kandungan teks
			$content = $tpl;
			if ( !$isContent ) {
				$content = file_get_contents( $tpl );
			}

		# Proses kandungan fail TPL
			$this->_removeComments( $content );// Buang kesemua komen
			$this->_storeLiterals( $content );// Simpan kesemua literal
			$this->_storePhpModes( $content );// Simpan kesemua teks dalam PHP mode
			$this->_storeTplTags( $content );// Tukarkan tag TPL kepada PHP dan simpan
			$this->_setFunctions();

		$this->_content = $this->_convert( $content );
	}

	private function _removeComments( &$content )//ok
	{
		$content = preg_replace( [ '/\|\*.*?\*\|/s', '/\|-.*/' ], '', $content );
	}

	private function _storeLiterals( &$content )//ok
	{
		$content = preg_replace_callback( '/\{l}.*?{\/l}|\{literal}.*?\{\/literal}/si',function( $tag )// dapatkan kesemua literal
						{
							$this->_literals[] = str_ireplace( [ '{l}', '{/l}', '{literal}', '{/literal}' ], '', $tag[0] );// tapis tag dan simpan teksnya
							
							$id = count( $this->_literals )-1;
							return "~~~literal[$id]~~~";//tandakan lokasi literal
						},
						$content );
	}

	private function _storePhpModes( &$content )
	{
		$content = preg_replace_callback(  [ '/{%.*?%}/s', '/{{.*?}}/s' ],function( $php )
			{
				$this->_phpModes[] = str_replace( [ '{%', '%}', '{{', '}}' ], [ '<?php ', ' ?>', '<?php ', ' ?>' ], $php[0] );// Gantikannya dengan tag PHP dan simpan kodnya
				
				$id = count( $this->_phpModes )-1;
				return "~~~php[$id]~~~";//tandakan lokasi kod PHP
			}, $content );
	}

	private function _storeTplTags( &$content )
	{
		$content = str_ireplace( [ '{else}',
								   '{/if}',
								   '{default}',
								   '{/switch}',
								   '{/foreach}',
								   '{/while}',
								   '{/for}',
								   '{break}',
								   '{continue}',
								   '{end}', //<-------------- View: Function (tanpa parameter)
								 ],
								 [ '<?php else: ?>',
								   '<?php endif; ?>',
								   '<?php default: ?>',
								   '<?php endswitch; ?>',
								   '<?php endforeach; ?>',
								   '<?php endwhile; ?>',
								   '<?php endfor; ?>',
								   '<?php break; ?>',
								   '<?php continue; ?>',
								   '<?php $this->end(); ?>',
								 ], $content );

		$content = preg_replace_callback( '/\{(?:(?>[^{}]+)|(?R))*\}/', function( $tag )// Cari tanda {  }
								{
									$this->_tags[] = preg_replace( [
																	# Struktur Kawalan
																		'/^{(if|elseif|switch|foreach|while|for)\b(.*?)}$/i',
																		'/^{(case)\b(.*?)}$/i',
																		'/^{(break|continue)\b(.*?)}$/i',
																	# View: Function
																	/**********************************
																		-{extend ''}
																		-{assign '',''}
																		-{start ''}
																		-{append ''}
																		-{prepend ''}
																		-{reset ''}
																		
																		-{fetch ''}
																		-{element ''}
																		-{cell ''}
																		-{cache ''}
																	***********************************/
																		'/^{(extend|assign|start|append|prepend|reset)\b\s(.*?)}$/i',
																		'/^{(fetch|element|cell|cache)\b\s(.*?)}$/i',
																	# echo Tag
																		'/^{(.*)}$/',
																	],
																	[
																		'<?php $1($2 ): ?>',
																		'<?php $1$2: ?>',
																		'<?php $1$2; ?>',

																		'<?php $this->$1( $2 ); ?>',
																		'<?php echo $this->$1( $2 ); ?>',

																		'<?php echo $1; ?>',
																	], $tag[0] );
									$id = count( $this->_tags )-1;
									return "~~~tag[$id]~~~";//tandakan lokasi kod PHP
								}, $content );
	}

	private function _setFunctions()
	{
		$set = function( &$content )
					{
						$content = preg_replace_callback( [ '/(?<!\w->)(?:\b\w+\(|\$\w+\b)/i' ], // Mengesan function dan variable (Helper) yang tidak bermula dengan tanda ->
									function( $matches )
									{
										return preg_replace( [
															   '/(?=\b(?:extend|assign|start|end|append|prepend|reset|fetch|element|cell|cache)\()/i',//View: Functions
															   '/\$(?=(?:request|Breadcrumbs|Flash|Form|Html|Number|Paginator|Rss|Text|Time|Url)\b)/',//Helper Umum
															   '/(?=\b(?:label|text|email|password|textarea|select|radio|checkbox|file|hidden|button)\()/i',//Helper: Form 1
															   '/\bform_(?=(?:create|end|error)\()/i', //Helper: Form 2
															   '/(?=\b(?:meta|css|nestedList|tableHeaders|tableCells)\()/i',//Helper: Html
															   '/\bjs\(/i',//Helper: Html - script
															   '/\bimg\(/i',//Helper: Html - image
															   '/\ba\(/i',//Helper: Html - link
															   '/\bcssV\(/i',//Helper: Html2 - css
															   '/\bjsV\(/i',//Helper: Html2 - script
															   '/\bimgV\(/i',//Helper: Html2 - image
															 ],
															 [
															   '$this->',
															   '$this->',
															   '$this->Form->',
															   '$this->Form->',
															   '$this->Html->',
															   '$this->Html->script(',
															   '$this->Html->image(',
															   '$this->Html->link(',
															   '$this->Html2->css(',
															   '$this->Html2->script(',
															   '$this->Html2->image(',
															 ], $matches[0] );
									}, $content );
					};
		
		$set( $this->_phpModes );
		$set( $this->_tags );
	}

	private function _convert( $content )
	{
		foreach ( $this->_tags as $i => $tag ) {
			$content = str_replace( "~~~tag[$i]~~~", $tag, $content );
		}
		foreach ( $this->_phpModes as $i => $php ) {
			$content = str_replace( "~~~php[$i]~~~", $php, $content );
		}
		foreach ( $this->_literals as $i => $literal ) {
			$content = str_replace( "~~~literal[$i]~~~", $literal, $content );
		}
		return $content;
	}

	public function get()
	{
		return $this->_content;
	}

	public function save( string $path )
	{
		# Pastikan direktori telahpun wujud terlebih dahulu
			$dir = dirname( $path );
			if ( !file_exists( $dir ) && !mkdir( $dir, 0777, TRUE ) ) return FALSE;
		
		return file_put_contents( $path, $this->_content );
	}
}

// $tpl = new TplParser( 'test.tpl' );
// echo $content = $tpl->get();
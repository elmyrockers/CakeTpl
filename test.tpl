|************************************************
      			KOMEN: MULTILINE				
*************************************************|
|---------------------------------------------------------------------------------- KOMEN: SINGLE-LINE




|------------------------------------------------| LITERAL
<a {l}data-attr='{"nama":"Ayu"}'{/l}></a>
{literal}
	Ini juga adalah literal
{/literal}


|------------------------------------------------| TAG PHP
{%
	$test1 = "Test sahaja";
%}
{{
	$test2 = "Hai";
	extend( 'something' );

	${'a'} = 23;
	$b = 2;


	$yeah = "seribu tahun takkan mungkin";
	{l}$wuhuu = "testing {{testing}}";{/l}
}}

{{""}}

{{   .*?   }}


|------------------------------------------------| STRUKTUR KAWALAN
	{if ${var} == 1} |----------------------------------- IF
		Ini adalah pernyataan pertama
	{elseif $var == 2}
		Ini adalah pernyataan ke-2
	{else}
		Ini adalah pernyataan ke-3
	{/if}


	{switch $var}
	{case 'Arnab'}
		{continue 2}
	{case 'Ayam'}
		{break 2}
	{case 'Kangaroo'}
		{continue}
	{default}
		{break}
	{/switch}

	{foreach $senarai_haiwan as $i => $haiwan} |------- FOREACH
		{break} |--- SEDANG BREAK
		{continue 1}
	{/foreach}
	{foreach range( 1, 10, 2 ) as $nombor}
		{$nombor}
	{/foreach}


	{while $var = $mysqli->fetch_array( ${query}} )}
	{/while}

	{for $a = 1; $a <= 10; $a+=2}
	{/for}
|------------------------------------------------| VIEW: FUNCTIONS 

{{extend()}}
{{assign()}}
{{start()}}
{{end() }}
{{append()}}
{{prepend()}}
{{reset()}}

{fetch( 'content' )}
{element()}
{cell()}
{cache()}

|*********** TERBARU ***********|
   {extend 'page.tpl'}
   {assign 'page.tpl','data'}
   {start 'blockName'}
   {end}
   {append 'blockName'}
   {prepend 'blockName'}
   {reset 'blockName'}

   {fetch 'blockName'}
   {element 'elementName'}
   {cell 'cellName'}
   {cache 'cacheName'}

|------------------------------------------------| HELPER UMUM
{$Breadcrumbs}
{$Flash}
{$Form}
{$Html}
{$Number}
{$Paginator}
{$Rss}
{$Text}
{$Time}
{$Url}

|------------------------------------------------| HELPER: FORM
{form_create()}

{label( 'user.name', 'Nama', [] )}
{text( 'user.name', [] )}
{email( 'user.email', [] )}
{password( 'user.password', [] )}
{textarea( 'message', [] )}
{select( 'user.state', [ options ], [] )}
{radio( 'user.sex', [ options ], [] )}
{checkbox( 'remember_me', [] )}
{file( 'user.picture', [] )}
{hidden( 'token', [] )}
{button( 'Hantar', [] )}

{form_end()}
{form_error( 'user.name' )}



|------------------------------------------------| HELPER: HTML
{meta( 'keywords', 'php, belajar php' )}
{css( 'forms', [] )}
{js( 'script', [] )}
{img( 'logo.png', [] )}
{a( 'Laman Utama', '/pages/home', [] )}

{nestedList( [] )}
{tableHeaders( [] )}
{tableCells( [] )}


{cssV( '/validation/forms' )}
{jsV( '/validation/forms' )}
{imgV( '/validation/forms.png' )}

|**********************************
***********************************|


quote dalam quote - 			("|')(?:\\\1|[^\1])*?\1
<?php
/*
 * Classe estática para ler e escrever arquivos e procurar textos
 * Autor: Lucas Costa
 * Data: Abr 2020
 */

Class Archive
{
	# Método para ler arquivos
	# @param $src:  aponta par aum fonte de texto
	public static function read ( string $src = "" ): string 
	{
		return ( file_exists ( $src ) ) ? file_get_contents ( $src ) : "{}";
	}

	# Método para escrever arquivos
	# flag "end": escreve ao fim do Arquivo 
	public static function write ( string $src = "", string $content = "", $flag = "init" ): bool
	{	
		$flag = ($flag  == "end" ) ? FILE_APPEND : null;
		return file_put_contents ( $src, $content, $flag );
	} 

	# Método para procurar textos
	public static function find ( string $scr = "", string $content = "" ): bool 
	{
		return ( strstr ( self::read ( $scr ),  $content ) );
	}

	# Método para alterar permissões em arquivos windows
	# ACLs: R (ler) | W (gravar) | C (Altear) | F (Controle total)
	# cacls: /E (Editar) | /G (Condece direitos) | /P (substitui direitos) | /R (revoga direitos) | /D (nega acessos)
	# Exemplo cacls f:\corporativo\Trocas /E /P Todos:C
	public static function permissionWin ( )
	{
		$exe = "cacls";
	}

	# Método para apagar um arquivo
	public static function erase ( $src )
	{
		unlink ( $src );
	}

	# Método para filtar conteudo entre duas ocorrencias
	public static function FilterBetween ( string $src, string $targetInit, string $targetEnd )
	{
		$out = "";
		
		if ( self::find ( $src, $targetInit )) {
			
			$targetEnd =  str_replace ( "</", "<\/",$targetEnd );

			$regex = "/{$targetInit}(.*?){$targetEnd}/s";

			$text = self::read ( $src );
			
			$status = preg_match($regex, $text, $matches);

			if ( $status == true ) {
				$out = $matches [ 0 ];
			}
		}

		return $out;

	}
	
	# Método para filtar conteúdo fora das ocorrencias selecionadas
	public static function filterOutside ( string $src, string $targetInit, string $targetEnd )
	{
		$out = "";
		
		if ( self::find ( $src, $targetInit )) {
			
			$targetEnd =  str_replace ( "</", "<\/",$targetEnd );

			$regex = "/{$targetInit}(.*?){$targetEnd}/s";

			$text = self::read ( $src );
			
			$status = preg_match($regex, $text, $matches);

			if ( $status == true ) {
				$buffer = explode ( $matches [ 0 ], $text );
				$out = implode ( "", $buffer);
			}
		}

		return $out;
	}

	# Método para juntar conteúdo de arquivos
	public static function join ( array $list = [ ] ): string 
	{	
		$compile = "";

		foreach ( $list as $index => $filename ) {
			$compile .= self::read ( $filename )."\r\n";
		};

		return $compile;
	}

	# Método para minificar os arquivos (retida espaços e quebras de linhas) 
	public static function minify ( $content = "" ): string
	{
		$compile = "";
		preg_match_all ( '/(\/\*)(.|\s)+?(\*\/)/', $content, $matches );

		foreach ( $matches [ 0 ] as $bloco ) {
			$compile = str_replace ( $bloco, '', $compile );
		};

		$compile = str_replace ( ' ',    '', $compile );
		$compile = str_replace ( "\r\n", '', $compile );
		$compile = str_replace ( "\r",   '', $compile );
		$compile = str_replace ( "\n",   '', $compile );
		$compile = str_replace ( ' ',    '', $compile );

		return $compile;
	}

}
<?php 
#Path.php
/*
 * Autor: Lucas Costa
 * Data: Abril 2020
 */

Class Path
{	
	# Normaliza o separador de diretório conforme o sistema operacional
	# @param $path : string
	# @return : string (type directory)
	public static function digest ( string $path = "" ): string 
	{
		$path = str_replace ( array ( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
		return $path;
	}

	# lista arquivos somente no diretório corrente
	# @param : string (type directory) - Exemplo: "*.{avi,mov,wmv,mp4,flv,mkv,rm}" (somente videos)
	# @return: array 
	public static function list ( string $path, string $flag = "*" ): array 
	{
		$files = [];

		$directory = glob( self::digest ( $path ).$flag, GLOB_BRACE );

		foreach ( $directory as $file ) {
			array_push ( $files, $file );
		};

		return $files;
	}

	# Scaneia diretórios
	public static function access ( string $path = "C:/", string $flag = "" ): array
	{ 
		$paths = array ( "directories"=> [], "videos"=> [] );

		$temp = glob( self::digest($path)."*" );
		$temp = array_filter ( $temp, function ( $dir ) {
			if ( is_dir ( $dir )) {
				return $dir;
			};
		} );

		$paths [ "directories" ] = $temp;
		$paths [ "videos" ] = glob( self::digest($path).$flag, GLOB_BRACE );

		return $paths;

	} 

	# verifica se o diretório existe
	public static function check ( string $directory = "" ): boolean
	{
		return ( is_dir ( self::digest ( $directory ) ) ) ? true : false;
	}
}

#$out = Path::digest ( "d:/lc/" );
#$out = Path::check ( "d:/lc/" );
#$out = Path::list ( "d:/lc/" );
#var_dump ( $out );
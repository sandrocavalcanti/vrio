<?

define ( "STR_REDUCE_LEFT", 1 );
define ( "STR_REDUCE_RIGHT", 2 );
define ( "STR_REDUCE_CENTER", 4 );

class Common {
	public static function addSlash($data) {
		return substr ( $data, 0, 2 ) . "/" . substr ( $data, 2, 2 ) . "/" . substr ( $data, 4, 4 );
	}
	public static function mysqlToBr($dataMysql) {
		// >>>	data mysql	2005-05-05
		// >>>	data normal	05-05-2005
		return substr ( $dataMysql, 8, 2 ) . "/" . substr ( $dataMysql, 5, 2 ) . "/" . substr ( $dataMysql, 0, 4 );
	}
	public static function mysqlToBrComHora($dataMysql) {
		// >>>	data mysql	2005-05-05 18:05:06
		// >>>	data normal	05-05-2005
		return substr ( $dataMysql, 8, 2 ) . "/" . substr ( $dataMysql, 5, 2 ) . "/" . substr ( $dataMysql, 0, 4 ) . " às " . substr ( $dataMysql, 11, 2 ) . ":" . substr ( $dataMysql, 14, 2 ) . ":" . substr ( $dataMysql, 17, 2 );
	}
	public static function getUFBr(){
		$uf = array
		(
				'PE'=>'PE',
				'AC'=>'AC',
				'AL'=>'AL',
				'AP'=>'AP',
				'AM'=>'AM',
				'BA'=>'BA',
				'CE'=>'CE',
				'DF'=>'DF',
				'ES'=>'ES',
				'GO'=>'GO',
				'MA'=>'MA',
				'MT'=>'MT',
				'MS'=>'MS',
				'MG'=>'MG',
				'PA'=>'PA',
				'PB'=>'PB',
				'PR'=>'PR',
				'PI'=>'PI',
				'RJ'=>'RJ',
				'RN'=>'RN',
				'RS'=>'RS',
				'RO'=>'RO',
				'RR'=>'RR',
				'SC'=>'SC',
				'SP'=>'SP',
				'SE'=>'SE',
				'TO'=>'TO'
		);
		return $uf;
	}
	public static function BrToMysql($dataBr) {
		return substr ( $dataBr, 6, 4 ) . "-" . substr ( $dataBr, 3, 2 ) . "-" . substr ( $dataBr, 0, 2 ) . " " . substr ( $dataBr, 11, 2 ) . ":" . substr ( $dataBr, 14, 2 ) . ":" . substr ( $dataBr, 17, 2 );
	}
	public static function BrToMysqlSemHora($dataBr) {
		return substr ( $dataBr, 6, 4 ) . "-" . substr ( $dataBr, 3, 2 ) . "-" . substr ( $dataBr, 0, 2 );
	}
	public static function getBrowseVersion() {
		$useragent = $_SERVER ['HTTP_USER_AGENT'];
		if (preg_match ( '|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched )) {
			$browser_version = $matched [1];
			$browser = 'IE';
		} elseif (preg_match ( '|Opera ([0-9].[0-9]{1,2})|', $useragent, $matched )) {
			$browser_version = $matched [1];
			$browser = 'Opera';
		} elseif (preg_match ( '|Firefox/([0-9\.]+)|', $useragent, $matched )) {
			$browser_version = $matched [1];
			$browser = 'Firefox';
		} elseif (preg_match ( '|Safari/([0-9\.]+)|', $useragent, $matched )) {
			$browser_version = $matched [1];
			$browser = 'Safari';
		} else {
			// browser not recognized!
			$browser_version = 0;
			$browser = 'other';
		}
		return array ($browser, $browser_version );
	}
	// TRATA SQL CONTRA SQL INJECTION
	public function anti_injection($sql) {
		# // remove palavras que contenham sintaxe sql
		$sql = preg_replace ( sql_regcase ( "/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/" ), "", $sql );
		$sql = trim ( $sql ); //limpa espa�os vazio
		$sql = strip_tags ( $sql ); //tira tags html e php
		$sql = addslashes ( $sql ); //Adiciona barras invertidas a uma string
		return $sql;
	}
	
	public static function getUniqid() {
		return substr ( md5 ( uniqid ( time () ) ), 0, 10 );
	}
	
	function formDate($date, $atual, $desejado) {
		$dataa = explode ( $atual, $date );
		$dataaa = $dataa [2] . $desejado . $dataa [1] . $desejado . $dataa [0];
		return $dataaa;
	}
	
	public static function InvalidString($text) {
		$b = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû";
		$a = "AaEeIiOoUuCcAaAaAaEeIiOoOoUu";
		$var = strtr ( substr ( $text, 1 ), $a, $b );
		$var = substr ( $text, 0, 1 ) . strtolower ( $var );
		return $var;
	}
	
	public static function InvalidCaracter($text) {
		$a = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû& -!@#$%¨&*()_+}=}{[^~?:;><,";
		$b = "AaEeIiOoUuCcAaAaAaEeIiOoOoUue____________________________";
		$var = strtr ( $text, $a, $b );
		$var = strtolower ( $var );
		return $var;
	}
	
	function formataNome($nome) {
		$nomefull = explode ( " ", $nome );
		$nomec = (strlen ( $nomefull [1] ) == 2 && substr ( $nomefull [1], 0, 1 ) == "D") ? $nomefull [0] . " " . $nomefull [1] . " " . $nomefull [2] : $nomefull [0] . " " . $nomefull [1];
		return ucwords ( strtolower ( Common::InvalidString ( $nomec ) ) );
	}
	
	function getDateLanguage() {
		
		$arrayday = array ("Domingo", "Segunda-Feira", "Terçaa-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "S�bado" );
		$arraymonth = array ("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" );
		$day = date ( "w" );
		$month = date ( "n" ) - 1;
		return $arrayday [$day] . ", " . date ( "d" ) . " de " . $arraymonth [$month] . " de " . date ( "Y" );
	}
	
	public static function redimensionarImagem($image_path, $width_max, $prefix) {
		define ( 'PATH_IMG', 'imgs' );
		//$MAX_HEIGHT = $height_max;
		$MAX_WIDTH = $width_max;
		# Calcular porcentagem da imagem
		list ( $width, $height, $type, $attr ) = getimagesize ( $image_path );
		$porcentagem = ($MAX_WIDTH * 100) / $width;
		$nova_altura = intval ( $height * ($porcentagem / 100) );
		
		$MAX_HEIGHT = $nova_altura;
		
		# Carrega a imagem
		$img = null;
		$tmp = explode ( '.', $image_path );
		$extensao = strtolower ( end ( $tmp ) );
		
		if ($extensao == 'jpg' || $extensao == 'jpeg') {
			$img = @imagecreatefromjpeg ( $image_path );
		} else if ($extensao == 'png') {
			$img = @imagecreatefrompng ( $image_path );
			// Se a vers�o do GD incluir suporte a GIF, mostra...
		} elseif ($extensao == 'gif') {
			$img = @imagecreatefromgif ( $image_path );
		}
		
		// Se a imagem foi carregada com sucesso, testa o tamanho da mesma
		if ($img) {
			// Pega o tamanho da imagem e propor��o de resize
			$width = imagesx ( $img );
			$height = imagesy ( $img );
			$scale = min ( $MAX_WIDTH / $width, $MAX_HEIGHT / $height );
			
			// Se a imagem � maior que o permitido, encolhe ela!
			if ($scale < 1) {
				$new_width = floor ( $scale * $width );
				$new_height = floor ( $scale * $height );
				// Cria uma imagem tempor�ria
				$tmp_img = imagecreatetruecolor ( $new_width, $new_height );
				// Copia e resize a imagem velha na nova
				imagecopyresampled ( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
				imagedestroy ( $img );
				$img = $tmp_img;
			}
		}
		
		// Cria uma imagem de erro se necess�rio
		if (! $img) {
			$img = imagecreate ( $MAX_WIDTH, $MAX_HEIGHT );
			imagecolorallocate ( $img, 204, 204, 204 );
			$c = imagecolorallocate ( $img, 153, 153, 153 );
			$c1 = imagecolorallocate ( $img, 0, 0, 0 );
			imageline ( $img, 0, 0, $MAX_WIDTH, $MAX_HEIGHT, $c );
			imageline ( $img, $MAX_WIDTH, 0, 0, $MAX_HEIGHT, $c );
			imagestring ( $img, 2, 12, 55, 'erro ao carregar imagem', $c1 );
		}
		
		//criando o nome da imagem redimensionada
		

		$new_image_path = dirname ( $image_path ) . "/" . $prefix . "." . $extensao;
		//$new_image_path = dirname($image_path)."/".$prefix."_".basename($image_path);
		

		// Salva a imagem
		imagejpeg ( $img, $new_image_path, 90 );
	}
	
	/*
*    function str_reduce (str $str, int $max_length [, str $append [, int $position [, bool $remove_extra_spaces ]]])
*
*    @return string
*
*    Reduz uma string sem cortar palavras ao meio. Pode-se reduzir a string pela
*    extremidade direita (padr�o da fun��o), esquerda, ambas ou pelo centro. Por
*    padr�o, ser�o adicionados tr�s pontos (...) � parte reduzida da string, mas
*    pode-se configurar isto atrav�s do par�metro $append.
*
*/
	public static function str_reduce($str, $max_length, $append = NULL, $position = STR_REDUCE_RIGHT, $remove_extra_spaces = true) {
		if (! is_string ( $str )) {
			echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 1 to be string.";
			return false;
		} else if (! is_int ( $max_length )) {
			echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 2 to be integer.";
			return false;
		} else if (! is_string ( $append ) && $append !== NULL) {
			echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 3 to be string.";
			return false;
		} else if (! is_int ( $position )) {
			echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 4 to be integer.";
			return false;
		} else if (($position != STR_REDUCE_LEFT) && ($position != STR_REDUCE_RIGHT) && ($position != STR_REDUCE_CENTER) && ($position != (STR_REDUCE_LEFT | STR_REDUCE_RIGHT))) {
			echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "(): The specified parameter '" . $position . "' is invalid.";
			return false;
		}
		
		if ($append === NULL) {
			$append = "...";
		}
		
		$str = html_entity_decode ( $str );
		
		if (( bool ) $remove_extra_spaces) {
			$str = preg_replace ( "/\s+/s", " ", trim ( $str ) );
		}
		
		if (strlen ( $str ) <= $max_length) {
			return htmlentities ( $str );
		}
		
		if ($position == STR_REDUCE_LEFT) {
			$str_reduced = preg_replace ( "/^.*?(\s.{0," . $max_length . "})$/s", "\\1", $str );
			
			while ( (strlen ( $str_reduced ) + strlen ( $append )) > $max_length ) {
				$str_reduced = preg_replace ( "/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced );
			}
			
			$str_reduced = $append . $str_reduced;
		} 

		else if ($position == STR_REDUCE_RIGHT) {
			$str_reduced = preg_replace ( "/^(.{0," . $max_length . "}\s).*?$/s", "\\1", $str );
			
			while ( (strlen ( $str_reduced ) + strlen ( $append )) > $max_length ) {
				$str_reduced = preg_replace ( "/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced );
			}
			
			$str_reduced .= $append;
		} 

		else if ($position == (STR_REDUCE_LEFT | STR_REDUCE_RIGHT)) {
			$offset = ceil ( (strlen ( $str ) - $max_length) / 2 );
			
			$str_reduced = preg_replace ( "/^.{0," . $offset . "}|.{0," . $offset . "}$/s", "", $str );
			$str_reduced = preg_replace ( "/^[^\s]+|[^\s]+$/s", "", $str_reduced );
			
			while ( (strlen ( $str_reduced ) + (2 * strlen ( $append ))) > $max_length ) {
				$str_reduced = preg_replace ( "/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced );
				
				if ((strlen ( $str_reduced ) + (2 * strlen ( $append ))) > $max_length) {
					$str_reduced = preg_replace ( "/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced );
				}
			}
			
			$str_reduced = $append . $str_reduced . $append;
		} 

		else if ($position == STR_REDUCE_CENTER) {
			$pattern = "/^(.{0," . floor ( $max_length / 2 ) . "}\s)|(\s.{0," . floor ( $max_length / 2 ) . "})$/s";
			
			preg_match_all ( $pattern, $str, $matches );
			
			$begin_chunk = $matches [0] [0];
			$end_chunk = $matches [0] [1];
			
			while ( (strlen ( $begin_chunk ) + strlen ( $append ) + strlen ( $end_chunk )) > $max_length ) {
				$end_chunk = preg_replace ( "/^\s?[^\s]+(\s.*)$/s", "\\1", $end_chunk );
				
				if ((strlen ( $begin_chunk ) + strlen ( $append ) + strlen ( $end_chunk )) > $max_length) {
					$begin_chunk = preg_replace ( "/^(.*?\s)[^\s]+\s?$/s", "\\1", $begin_chunk );
				}
			}
			
			$str_reduced = $begin_chunk . $append . $end_chunk;
		}
		
		return $str_reduced;
	} //fim metodo

	// converte valor 1.000,00 para 1000.00
	public static function valorBrMysql($valor) {
		$valor = str_replace(".","",$valor);
		$valor = str_replace(",",".",$valor);
	
		return $valor;
	}
	/********************************************************************/
	/* Funcao: void masc_cpf(string cpf)								   							*/
	/*		   Recebe um cpf no formato xxxxxxxxxxx e retorna no					*/
	/*		   formato xxx.xxx.xxx-xx				       												*/
	/********************************************************************/
	public static function mascCpf($cpf) {
		if($cpf)
			$retorno = substr($cpf,0,3) . "." . substr($cpf,3,3) . "." . substr($cpf,6,3) . "-" . substr($cpf,9,2);
		else
			$retorno = "";
		return($retorno);
	}
	
	/********************************************************************/
	/* Funcao: void unmasc_cpf(string cpf)								   						*/
	/*		   Recebe um cpf no formato xxx.xxx.xxx-xx e retorna no				*/
	/*		   formato xxxxxxxxxxx				       													*/
	/********************************************************************/
	public static function unmascCpf($cpf) {
		$remover = array(".","-");
		$retorno = str_replace($remover,"",$cpf);
		return($retorno);
	}
	
}
?>
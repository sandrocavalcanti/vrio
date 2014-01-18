<?php

define("STR_REDUCE_LEFT", 1);
define("STR_REDUCE_RIGHT", 2);
define("STR_REDUCE_CENTER", 4);

class Common {
    
    /**
      * Função para limitar o texto de uma notícia/agenda/etc para exibição sem quebra de layout
      * @param string $string
      * @param int $limit
      * @return $string
      */
    public static function limitarConteudo ($string, $limit = 90) {
     	// Limpar tags html
     	$string = strip_tags($string);
     	if (strlen($string) <= $limit) {
     		return $string;
     	} else {
     		$string = substr($string, 0, $limit) . "...";
     	}
     	return $string;
    }
    
    /**
     * Método para pegar o ID do vídeo do YouTube
     * @param string $link
     * @return string 
     */
    public static function youtubeID($link) {
        $matches = array();
        $regra = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/";
        preg_match($regra, $link, $matches);
		return $matches[7];
    }
    
    public static function meses () {
        return array("Janeiro", "Fevereiro", "Março", "Abril", "Maio",
        "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro",
        "Dezembro");
    }

	public static function recupearMes($cod){
        $arraymonth = array(1=>"Janeiro", 2=>"Fevereiro", 3=>"Março", 4=>"Abril", 5=>"Maio", 6=>"Junho", 7=>"Julho", 8=>"Agosto", 9=>"Setembro", 10=>"Outubro", 11=>"Novembro", 12=>"Dezembro");

        return $arraymonth[$cod];
    }
    
    public static function formataNomeArquivo ($string) {
        /*$text = strtr($text, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_");
        $var = preg_replace("[^a-zA-Z0-9_]", "", $text);
        //$var = preg_replace("/[^0-9a-zA-Z\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\/\_\-]/i'", "_", $text);
        return $var;*/
        
        // Replace spaces with underscores and makes the string lowercase
        $string = str_replace (" ", "_", $string);

        $string = str_replace ("..", ".", $string);
        $string = strtolower ($string);

        // Match any character that is not in our whitelist
        preg_match_all ("/[^0-9^a-z^_^.]/", $string, $matches);

        // Loop through the matches with foreach
        foreach ($matches[0] as $value) {
            $string = str_replace($value, "", $string);
        }
        return $string;
    }
    public static function urlAmigavel($str) {
        // action body
        $str = trim($str);
        $str = self::removeAcentos($str);
        $str = preg_replace("/[^a-zA-Z]\s/", "-", $str);
        $str = preg_replace('/ /', '-', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('\`', '', $str);
        $str = str_replace('\´', '', $str);
        $fc  = Zend_Controller_Front::getInstance();
        return $str;
    }
    
    public static function removeAcentos($str, $enc = "UTF-8") {
        $acentos = array(
            'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'C' => '/&Ccedil;/', 'c' => '/&ccedil;/',
            'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/', 'N' => '/&Ntilde;/',
            'n' => '/&ntilde;/',
            'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/', 'Y' => '/&Yacute;/',
            'y' => '/&yacute;|&yuml;/', 'a.' => '/&ordf;/', 'o.' => '/&ordm;/' );
        return strtolower(
                        preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc)));
    }

    public static function addSlash($data) {
        return substr($data, 0, 2) . "/" . substr($data, 2, 2) . "/" .
                substr($data, 4, 4);
    }

    public static function mysqlToBr($dataMysql) {
        // >>>	data mysql	2005-05-05
        // >>>	data normal	05-05-2005
        return substr($dataMysql, 8, 2) . "/" . substr($dataMysql, 5, 2) . "/" . substr($dataMysql, 0, 4);
    }

    public static function mysqlToBrComHora($dataMysql) {
        // >>>	data mysql	2005-05-05 18:05:06
        // >>>	data normal	05-05-2005
        return substr($dataMysql, 8, 2) . "/" .
                substr($dataMysql, 5, 2) . "/" . substr($dataMysql, 0, 4) . " ás " .
                substr($dataMysql, 11, 2) . ":" . substr($dataMysql, 14, 2) . ":" .
                substr($dataMysql, 17, 2);
    }

    public static function getUFBr() {
        $uf = array( 'PE' => 'PE', 'AC' => 'AC', 'AL' => 'AL', 'AP' => 'AP',
            'AM' => 'AM', 'BA' => 'BA', 'CE' => 'CE', 'DF' => 'DF', 'ES' => 'ES',
            'GO' => 'GO', 'MA' => 'MA', 'MT' => 'MT', 'MS' => 'MS', 'MG' => 'MG',
            'PA' => 'PA', 'PB' => 'PB', 'PR' => 'PR', 'PI' => 'PI', 'RJ' => 'RJ',
            'RN' => 'RN', 'RS' => 'RS', 'RO' => 'RO', 'RR' => 'RR', 'SC' => 'SC',
            'SP' => 'SP', 'SE' => 'SE', 'TO' => 'TO' );
        return $uf;
    }

    public static function BrToMysql($dataBr) {
        return substr($dataBr, 6, 4) . "-" . substr($dataBr, 3, 2) . "-" .
                substr($dataBr, 0, 2) . " " . substr($dataBr, 11, 2) . ":" .
                substr($dataBr, 14, 2) . ":" . substr($dataBr, 17, 2);
    }

    public static function BrToMysqlSemHora($dataBr) {
        return substr($dataBr, 6, 4) . "-" . substr($dataBr, 3, 2) . "-" .
                substr($dataBr, 0, 2);
    }

    public static function getBrowseVersion() {
        $useragent = $_SERVER[ 'HTTP_USER_AGENT' ];
        if ( preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched) ) {
            $browser_version = $matched[ 1 ];
            $browser         = 'IE';
        } elseif ( preg_match('|Opera ([0-9].[0-9]{1,2})|', $useragent, $matched) ) {
            $browser_version = $matched[ 1 ];
            $browser         = 'Opera';
        } elseif ( preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched) ) {
            $browser_version = $matched[ 1 ];
            $browser         = 'Firefox';
        } elseif ( preg_match('|Safari/([0-9\.]+)|', $useragent, $matched) ) {
            $browser_version = $matched[ 1 ];
            $browser         = 'Safari';
        } else {
            // browser not recognized!
            $browser_version = 0;
            $browser         = 'other';
        }
        return array( $browser, $browser_version );
    }

    // TRATA SQL CONTRA SQL INJECTION
    public function anti_injection($sql) {
        # // remove palavras que contenham sintaxe sql
        $sql = preg_replace(
                sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"), "", $sql);
        $sql = trim($sql); //limpa espa�os vazio
        $sql = strip_tags($sql); //tira tags html e php
        $sql = addslashes($sql); //Adiciona barras invertidas a uma string
        return $sql;
    }

    public static function getUniqid() {
        return substr(md5(uniqid(time())), 0, 10);
    }

    function formDate($date, $atual, $desejado) {
        $dataa  = explode($atual, $date);
        $dataaa = $dataa[ 2 ] . $desejado . $dataa[ 1 ] . $desejado . $dataa[ 0 ];
        return $dataaa;
    }

    public static function InvalidString($text) {
        $b   = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû";
        $a   = "AaEeIiOoUuCcAaAaAaEeIiOoOoUu";
        $var = strtr(substr($text, 1), $a, $b);
        $var = substr($text, 0, 1) . strtolower($var);
        return $var;
    }

    public static function InvalidCaracter($text) {
        $a   = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû& -!@#$%¨&*()_+}=}{[^~?:;><,";
        $b   = "AaEeIiOoUuCcAaAaAaEeIiOoOoUue____________________________";
        $var = strtr($text, $a, $b);
        $var = strtolower($var);
        return $var;
    }

    function formataNome($nome) {
        $nomefull = explode(" ", $nome);
        $nomec    = (strlen($nomefull[ 1 ]) == 2 &&
                substr($nomefull[ 1 ], 0, 1) == "D") ? $nomefull[ 0 ] . " " .
                $nomefull[ 1 ] . " " . $nomefull[ 2 ] : $nomefull[ 0 ] . " " . $nomefull[ 1 ];
        return ucwords(strtolower(Common::InvalidString($nomec)));
    }

    function getDateLanguage() {
        $arrayday = array( "Domingo", "Segunda-Feira", "Terçaa-Feira",
            "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "S�bado" );
        $arraymonth = array( "Janeiro", "Fevereiro", "Março", "Abril", "Maio",
            "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro",
            "Dezembro" );
        $day   = date("w");
        $month = date("n") - 1;
        return $arrayday[ $day ] . ", " . date("d") . " de " .
                $arraymonth[ $month ] . " de " . date("Y");
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
        if ( !is_string($str) ) {
            echo "<br /><strong>Warning</strong>: " . __FUNCTION__ .
            "() expects parameter 1 to be string.";
            return false;
        } else
        if ( !is_int($max_length) ) {
            echo "<br /><strong>Warning</strong>: " . __FUNCTION__ .
            "() expects parameter 2 to be integer.";
            return false;
        } else
        if ( !is_string($append) && $append !== NULL ) {
            echo "<br /><strong>Warning</strong>: " . __FUNCTION__ .
            "() expects optional parameter 3 to be string.";
            return false;
        } else
        if ( !is_int($position) ) {
            echo "<br /><strong>Warning</strong>: " . __FUNCTION__ .
            "() expects optional parameter 4 to be integer.";
            return false;
        } else
        if ( ($position != STR_REDUCE_LEFT) &&
                ($position != STR_REDUCE_RIGHT) &&
                ($position != STR_REDUCE_CENTER) &&
                ($position != (STR_REDUCE_LEFT | STR_REDUCE_RIGHT)) ) {
            echo "<br /><strong>Warning</strong>: " .
            __FUNCTION__ . "(): The specified parameter '" .
            $position . "' is invalid.";
            return false;
        }
        if ( $append === NULL ) {
            $append = "...";
        }
        $str    = html_entity_decode($str);
        if ( (bool) $remove_extra_spaces ) {
            $str = preg_replace("/\s+/s", " ", trim($str));
        }
        if ( strlen($str) <= $max_length ) {
            return htmlentities($str);
        }
        if ( $position == STR_REDUCE_LEFT ) {
            $str_reduced = preg_replace("/^.*?(\s.{0," . $max_length . "})$/s", "\\1", $str);
            while ( (strlen($str_reduced) + strlen($append)) > $max_length ) {
                $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
            }
            $str_reduced = $append . $str_reduced;
        } else
        if ( $position == STR_REDUCE_RIGHT ) {
            $str_reduced = preg_replace(
                    "/^(.{0," . $max_length . "}\s).*?$/s", "\\1", $str);
            while ( (strlen($str_reduced) + strlen($append)) > $max_length ) {
                $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);
            }
            $str_reduced .= $append;
        } else
        if ( $position == (STR_REDUCE_LEFT | STR_REDUCE_RIGHT) ) {
            $offset      = ceil((strlen($str) - $max_length) / 2);
            $str_reduced = preg_replace(
                    "/^.{0," . $offset . "}|.{0," . $offset . "}$/s", "", $str);
            $str_reduced = preg_replace("/^[^\s]+|[^\s]+$/s", "", $str_reduced);
            while ( (strlen($str_reduced) + (2 * strlen($append))) >
            $max_length ) {
                $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);
                if ( (strlen($str_reduced) + (2 * strlen($append))) >
                        $max_length ) {
                    $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
                }
            }
            $str_reduced = $append . $str_reduced . $append;
        } else
        if ( $position == STR_REDUCE_CENTER ) {
            $pattern     = "/^(.{0," . floor($max_length / 2) .
                    "}\s)|(\s.{0," . floor($max_length / 2) . "})$/s";
            preg_match_all($pattern, $str, $matches);
            $begin_chunk = $matches[ 0 ][ 0 ];
            $end_chunk   = $matches[ 0 ][ 1 ];
            while ( (strlen($begin_chunk) + strlen($append) +
            strlen($end_chunk)) > $max_length ) {
                $end_chunk = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $end_chunk);
                if ( (strlen($begin_chunk) + strlen($append) +
                        strlen($end_chunk)) > $max_length ) {
                    $begin_chunk = preg_replace(
                            "/^(.*?\s)[^\s]+\s?$/s", "\\1", $begin_chunk);
                }
            }
            $str_reduced = $begin_chunk . $append . $end_chunk;
        }
        return $str_reduced;
    }

//fim metodo
}

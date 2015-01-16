<?php namespace helpers;  

    class Security{

        /**
        *
        * Método encargado de encriptar una cadena.
        *
        * @param string $string Cadena a encriptar.
        * @param string $secLevel Nivel de seguridad de encriptación (1: Normal, 2: Alto).
        *
        * @return string Cadena encriptada.
        *
        */

            public function encriptar( $string, $secLevel = 2 )
            {

                if( $secLevel == 1 ){

                    $resultado = '';

                    for( $i=0; $i<strlen($string); $i++ ){

                        $char       = substr($string, $i, 1);
                        $keychar    = substr(ENCRYPTION_KEY, ($i % strlen(ENCRYPTION_KEY)) - 1, 1);
                        $char       = chr(ord($char)+ord($keychar));
                        $resultado .= $char;
                    
                    }

                    return base64_encode($resultado);

                } elseif( $secLevel == 2 ){

                    if( !$td = mcrypt_module_open('rijndael-256', '', 'ctr', '') )
                        return false;

                    $string = serialize($string);
                    $iv     = mcrypt_create_iv(32, MCRYPT_RAND);

                    if( mcrypt_generic_init($td, ENCRYPTION_KEY, $iv) !== 0 )
                        return false;

                    $string = mcrypt_generic($td, $string);
                    $string = $iv . $string;

                    $mac = self::pbkdf2($string, ENCRYPTION_KEY, 1000, 32);

                    $string .= $mac;

                    mcrypt_generic_deinit($td);
                    mcrypt_module_close($td);

                    $encriptado = base64_encode($string);

                    return $encriptado;

                }

            }

        /**
        *
        * Método encargado de desencriptar una cadena.
        *
        * @param string $string Cadena a desencriptar.
        * @param string $secLevel Nivel de seguridad de encriptación (1: Normal, 2: Alto).
        *
        * @return string Cadena desencriptada.
        *
        */

            public function desencriptar( $string, $secLevel = 2 )
            {

                if( $secLevel == 1 ){

                    $resultado = '';
                    $string = base64_decode($string);

                    for( $i=0; $i<strlen($string); $i++ ){
                        
                        $char       = substr($string, $i, 1);
                        $keychar    = substr(ENCRYPTION_KEY, ($i % strlen(ENCRYPTION_KEY)) - 1, 1);
                        $char       = chr(ord($char) - ord($keychar));
                        $resultado .= $char;
                    
                    }

                    return $resultado;

                } elseif( $secLevel == 2 ){

                    $string = base64_decode($string);

                    if ( !$td = mcrypt_module_open('rijndael-256', '', 'ctr', '') )
                        return false;

                    $iv = substr($string, 0, 32);
                    $mo = strlen($string) - 32;
                    $em = substr($string, $mo);

                    $string = substr($string, 32, strlen($string) - 64);
                    $mac = self::pbkdf2($iv . $string, ENCRYPTION_KEY, 1000, 32);

                    if( $em != $mac )
                        return false;

                    if( mcrypt_generic_init($td, ENCRYPTION_KEY, $iv) !== 0 )
                        return false;

                    $string = mdecrypt_generic($td, $string);
                    $string = unserialize($string);

                    mcrypt_generic_deinit($td);
                    mcrypt_module_close($td);

                    return $string;

                }

            }

        /**
        *
        * Método utilizado por los dos métodos anteriores para dar aleatoriedad.
        *
        * @param string $p Contraseña.
        * @param string $s Semilla.
        * @param int $c Número de iteraciones.
        * @param int $kl Tamaño de la "Key".
        * @param string $a Algoritmo de hash.
        *
        * @return string "Key" aleatoria.
        *
        */

            public function pbkdf2( $p, $s, $c, $kl, $a = 'sha256' )
            {

                $hl = strlen(hash($a, null, true));
                $kb = ceil($kl / $hl);
                $dk = '';

                for( $block = 1; $block <= $kb; $block++ ){

                    $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

                    for( $i = 1; $i < $c; $i++ )

                        $ib ^= ( $b = hash_hmac($a, $b, $p, true) );

                    $dk .= $ib;

                }

                return substr($dk, 0, $kl);

            }

        /**
        *
        * Método encargado de limpiar los INPUT.
        *
        * @param string $value Texto a limpiar.
        *
        * @return string Texto limpio.
        *
        */

            public function cleanInput( $value )
            {

                if(is_array($value)){

                    foreach( $value as $key => $val ) {

                        $value[$key] = cleanInput($val);

                    }

                    return $value;

                } else {

                    return strip_tags(trim($value));
                
                }

            }

    }
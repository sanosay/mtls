<?php

namespace com\snsp;

/**
 * Represents a utility class containing URL 
 * specific helper methods
 * <i>Note: smells a lot. I wish could use at least regex</i>
 * @author sanosay
 */
class UrlHelper {

    const URL_COMPONENTS = -1;
    const URL_SCHEME = 1;
    const URL_HOST = 2;
    const URL_PORT = 3;
    const URL_USER = 4;
    const URL_PASS = 5;
    const URL_PATH = 6;
    const URL_QUERY = 7;
    const URL_FRAGMENT = 8;
    const VALID_URL_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~:/?#[]@!$&\'()*+,;=%';

    

    /**
     * Parse a URL and return its components
     * @param string $str The URL to parse. Invalid characters are replaced by_
     * @param int $components  A specific component from the provided url
     * returns 
     */
    public static function ParseUrl($str, $components = UrlHelper::URL_COMPONENTS) {

        for ($i = 0; $i < strlen($str); $i++) {

            if (strpos(UrlHelper::VALID_URL_CHARS, $str[$i]) <0) {
                $str[$i] = '_';
            }
        }
        $retVal = array();

        $scheme = static::extractScheme($str);
        $str = $scheme['remain'];
        if ($scheme['scheme'] != null) {
            $retVal['scheme'] = $scheme['scheme'];
        }
        
        if (strlen($str) > 0) {
            $usernameAndPassword = static::extractUsernamePassword($str,isset($scheme['scheme']));
            $str = $usernameAndPassword['remain'];
            if ($usernameAndPassword['username'] != null) {
                $retVal['user'] = $usernameAndPassword['username'];
                if ($usernameAndPassword['password'] != null) {
                    $retVal['pass'] = $usernameAndPassword['password'];
                }
            }
        }
        if (strlen($str) > 0) {
            $hostPort = static::ExtractHostAndPort($str);
            if($hostPort['host']!=null){
                $retVal['host'] = $hostPort['host'];
                if($hostPort['port']!=null){
                    $retVal['port'] = $hostPort['port'];
                }
            }
            $str = $hostPort['remain'];
        }
        if(strlen($str)>0){
            $path = static::ExtractPath($str);
            if($path['path']!=null){
                $retVal['path'] = $path['path'];
            }
            $str = $path['remain'];
        }
        if(strlen($str)>0){
            $queryFragment = static::ExtractQueryAndFragment($str);
            if($queryFragment['query']!=null){
                $retVal['query'] = $queryFragment['query'];
            }
            if($queryFragment['fragment']!=null){
                $retVal['fragment'] = $queryFragment['fragment'];
            }
            $str = $queryFragment['remain'];
        }
        if (UrlHelper::URL_COMPONENTS) {
            return $retVal;
        } else if (UrlHelper::URL_SCHEME) {
            return $retVal['scheme'];
        } else if (UrlHelper::URL_HOST) {
            return $retVal['host'];
        } else if (UrlHelper::URL_PORT) {
            return $retVal['port'];
        } else if (UrlHelper::URL_USER) {
            return $retVal['user'];
        } else if (UrlHelper::URL_PASS) {
            return $retVal['pass'];
        } else if (UrlHelper::URL_PATH) {
            return $retVal['path'];
        } else if (UrlHelper::URL_QUERY) {
            return $retVal['query'];
        } else if (UrlHelper::URL_FRAGMENT) {
            return $retVal['fragment'];
        }

        return null;
    }

    /**
     * Extracts the scheme from a url
     * @param string $str The url
     * @return mixed [scheme=> The extracted value, remain=> The remaining string
     */
    public static function ExtractScheme($str) {
        $ret = array();
        $pos = strpos($str, '://');
        $scheme = null;
        if ($pos > 0) {

            $scheme = substr($str, 0, $pos);
            $str = substr($str, $pos + 3);
        }
        return array('scheme' => $scheme, 'remain' => $str);
    }

    /**
     * Extracts the username and password from a url
     * @param string $str The url
     * @return mixed [username => The username, password => The password remain => The remaining string
     */
    public static function ExtractUsernamePassword($str) {
        $pos = strpos($str, '@');
        $username = null;
        $password = null;
        if ($pos > 0) {
            $temp = substr($str, 0, $pos);
            $str = substr($str, $pos + 1);
            $passwordPos = strpos($temp, ':');
            if ($passwordPos > 0) {
                $usrpsw = explode(':', $temp);
                $username = $usrpsw[0];
                $password = $usrpsw[1];
            } else {
                $username = $temp;
            }
        }
        return array('username' => $username, 'password' => $password, 'remain' => $str);
    }

    /**
     * Extracts the host and the port from a url
     * @param string $str The url
     * @param boolean $hadScheme (optional) Determine if the supplied url had a scheme
     * @return mixed [host=> The extracted host, port=> The extracted port, remain=> The remaining string
     */
    public static function ExtractHostAndPort($str,$hadScheme=false) {
        $ret = array();
        $pos = strpos($str, '/');
        if ($pos <= 0) {
            $pos = strpos($str, '?');

            if ($pos <= 0) {
                $pos = strpos($str, '#');
                if ($pos <= 0) {
                    $pos = strlen($str);
                }
            }
        }
        $host = null;
        $port = null;
        $temp = $str;
        if ($pos > 0) {
            $temp = substr($str, 0, $pos);
        }
        if (strlen($temp) > 0) {
            $portPos = strpos($str, ':');
           
            if ($portPos > 0) {
                $hostAndPort = explode(':', $temp);
                $host = $hostAndPort[0];
                
                $port = intval($hostAndPort[1]);
                $str = substr($str, $pos == 0 ? strlen($str) : $pos);
            } else {
                if(strpos($temp,'.')>0 || $hadScheme){
                    $host = $temp;
                    $str = substr($str, $pos == 0 ? strlen($str) : $pos);
                }else{
                    $host = '';
                    
                }
            }
        }

        return array('host' => $host, 'port' => $port, 'remain' => $str);
    }

    /**
     * Extracts the path from a url
     * @param string $str The url
     * @return mixed [path=> The extracted value, remain=> The remaining string
     */
    public static function ExtractPath($str) {
        $ret = array();
        $pos = strpos($str, '?');
        if ($pos <= 0) {
            $pos = strpos($str, '#');

            if ($pos <= 0) {
                //$pos = strrpos($str, '/');
                $pos = strlen($str);
                //
            }
        } 
        $path = null;

        $temp = $str;
        if ($pos > 0) {
            $temp = substr($str, 0, $pos);
        }
        if (strlen($temp) > 0) {

            $str = substr($str, $pos == 0 ? strlen($str) : $pos);
            $path = $temp;
        }

        return array('path' => $path, 'remain' => $str);
    }

    /**
     * Extracts the query from a url
     * @param string $str The url
     * @return mixed [query=> The extracted value, fragment=> The extracted fragment, remain=> The remaining string
     */
    public static function ExtractQueryAndFragment($str) {
        $ret = array();
        $pos = strpos($str, '?');
        $query = null;
        $fragment = null;
        if ($pos >= 0) {

            $temp = substr($str, $pos + 1);
            $fragmentPos = strpos($temp, '#');
            if ($fragmentPos > 0) {
                $query = substr($temp, $pos, $fragmentPos);
                $fragment = substr($temp, $fragmentPos + 1);
            } else {
                $query = $temp;
            }
        } else {
            if (strpos($str, '#') > 0) {
                $fragment = substr($temp, $fragmentPos + 1);
            }
        }

        return array('query' => $query, 'fragment' => $fragment, 'remain' => '');
    }

    private static function contains($str, $needle) {
        return strpos($str, $needle) >= 0;
    }

}

<?php
namespace App\Libraries;

class SMS
{
    public static function send($number, $message)
    {
        $gupshup_account = array('username'=>'2000030788','password'=>'6BeNqpFy6');
        
        $gupshup_param = array(
            'method'	=>	'sendMessage',
            'v'			=>	'1.1',
            'msg_type'	=>	'TEXT',
            'auth_scheme'=>	'PLAIN',
            'mask'		=>	'MAD',
            'userid'	=>	$gupshup_account['username'],
            'password'	=>	$gupshup_account['password'],
            'msg'       =>  $message,
            'send_to'   =>  $number
        );

        //$url = str_replace('&amp;', '&', url('http://enterprise.smsgupshup.com/GatewayAPI/rest', $gupshup_param));
        $url = str_replace('&amp;', '&', SMS::getLink('http://enterprise.smsgupshup.com/GatewayAPI/rest', $gupshup_param));

        // Comment the line below to disable Messageing
        $data = SMS::load($url);

        return true;
    }

    //Function to make the above code work :)
    public static function getLink($url, $params=array(), $use_existing_arguments=false)
    {
        if (!$params and !$use_existing_arguments) {
            return $url;
        }
        if ($use_existing_arguments) {
            $params = $params + $_GET;
        }

        $link = $url;

        if (strpos($link, '?') === false) {
            $existing_parameters = array();
        } else { // This will make sure that even if the specified param exists in the given url, it will be over written.
            $url_parts = explode('?', $url);
            $link = $url_parts[0];
            $existing_parameters = array();

            if ($url_parts[1]) {
                $all_url_parameters = preg_split("/\&(amp\;)?/", $url_parts[1]);
                foreach ($all_url_parameters as $part) {
                    list($name, $value) = explode("=", $part);
                    $existing_parameters[$name] = $value;
                }
            }
        }
        if ($existing_parameters) {
            $params = $params + $existing_parameters;
        }

        $params_arr = array();
        foreach ($params as $key=>$value) {
            if ($value === null) {
                continue;
            } // If the value is given as null, don't show it in the query at all. Use arg=>"null" if you want a string null in the query.
            if ($use_existing_arguments) {// Success or Error message don't have to be shown.
                if (($key == 'success' and isset($_GET['success']) and $_GET['success'] == $value)
                    or ($key == 'error' and isset($_GET['error']) and $_GET['error'] == $value)) {
                    continue;
                }
            }

            if (gettype($value) == 'array') { //Handle array data properly
                foreach ($value as $val) {
                    $params_arr[] = $key . '[]=' . urlencode($val);
                }
            } else {
                $params_arr[] = $key . '=' . urlencode($value);
            }
        }
        if ($params_arr) {
            $link = $link . '?' . implode('&amp;', $params_arr);
        }

        return $link;
    }

    public static function load($url, $options=array())
    {
        $default_options = array(
            'method'		=> 'get',
            'post_data'		=> array(),		// The data that must be send to the URL as post data.
            'return_info'	=> false,		// If true, returns the headers, body and some more info about the fetch.
            'return_body'	=> true,		// If false the function don't download the body - useful if you just need the header or last modified instance.
            'cache'			=> false,		// If true, saves a copy to a local file - so that the file don't have multiple times.
            'cache_folder'	=> '/tmp/php-load-function/', // The folder to where the cache copy of the file should be saved to.
            'cache_timeout'	=> 0,			// If the cached file is older that given time in minutes, it will download the file again and cache it.
            'referer'		=> '',			// The referer of the url.
            'headers'		=> array(),		// Custom headers
            'session'		=> false,		// If this is true, the following load() calls will use the same session - until load() is called with session_close=true.
            'session_close'	=> false,
        );
        // Sets the default options.
        foreach ($default_options as $opt=>$value) {
            if (!isset($options[$opt])) {
                $options[$opt] = $value;
            }
        }

        $url_parts = parse_url($url);
        $ch = false;
        $info = array(//Currently only supported by curl.
            'http_code'	=> 200
        );
        $response = '';


        $send_header = array(
                'User-Agent' => 'BinGet/1.00.A (http://www.bin-co.com/php/scripts/load/)'
            ) + $options['headers']; // Add custom headers provided by the user.

        if ($options['cache']) {
            $cache_folder = $options['cache_folder'];
            if (!file_exists($cache_folder)) {
                $old_umask = umask(0); // Or the folder will not get write permission for everybody.
                mkdir($cache_folder, 0777);
                umask($old_umask);
            }

            $cache_file_name = md5($url) . '.cache';
            $cache_file = joinPath($cache_folder, $cache_file_name); //Don't change the variable name - used at the end of the function.

            if (file_exists($cache_file) and filesize($cache_file) != 0) { // Cached file exists - return that.
                $timedout = false;
                if ($options['cache_timeout']) {
                    if (((time() - filemtime($cache_file)) / 60) > $options['cache_timeout']) {
                        $timedout = true;
                    }  // If the cached file is older than the timeout value, download the URL once again.
                }

                if (!$timedout) {
                    $response = file_get_contents($cache_file);

                    //Seperate header and content
                    $seperator_charector_count = 4;
                    $separator_position = strpos($response, "\r\n\r\n");
                    if (!$separator_position) {
                        $separator_position = strpos($response, "\n\n");
                        $seperator_charector_count = 2;
                    }
                    // If the real seperator(\r\n\r\n) is NOT found, search for the first < char.
                    if (!$separator_position) {
                        $separator_position = strpos($response, "<"); //:HACK:
                        $seperator_charector_count = 0;
                    }

                    $body = '';
                    $header_text = '';
                    if ($separator_position) {
                        $header_text = substr($response, 0, $separator_position);
                        $body = substr($response, $separator_position+$seperator_charector_count);
                    }

                    foreach (explode("\n", $header_text) as $line) {
                        $parts = explode(": ", $line);
                        if (count($parts) == 2) {
                            $headers[$parts[0]] = chop($parts[1]);
                        }
                    }
                    $headers['cached'] = true;

                    if (!$options['return_info']) {
                        return $body;
                    } else {
                        return array('headers' => $headers, 'body' => $body, 'info' => array('cached'=>true));
                    }
                }
            }
        }

        ///////////////////////////// Curl /////////////////////////////////////
        //If curl is available, use curl to get the data.
        if (function_exists("curl_init")
            and (!(isset($options['use']) and $options['use'] == 'fsocketopen'))) { //Don't use curl if it is specifically stated to use fsocketopen in the options

            if (isset($options['post_data']) and $options['post_data']) { //There is an option to specify some data to be posted.
                $page = $url;
                $options['method'] = 'post';

                if (is_array($options['post_data'])) { //The data is in array format.
                    $post_data = array();
                    foreach ($options['post_data'] as $key=>$value) {
                        if ($value) {
                            $post_data[] = "$key=" . urlencode($value);
                        } else {
                            $post_data[] = $key;
                        }
                    }
                    $url_parts['query'] = implode('&', $post_data);
                } else { //Its a string
                    $url_parts['query'] = $options['post_data'];
                }
            } else {
                if (isset($options['method']) and $options['method'] == 'post') {
                    $page = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
                } else {
                    $page = $url;
                }
            }

            if ($options['session'] and isset($GLOBALS['_binget_curl_session'])) {
                $ch = $GLOBALS['_binget_curl_session'];
            } //Session is stored in a global variable
            else {
                $ch = curl_init($url_parts['host']);
            }

            curl_setopt($ch, CURLOPT_URL, $page) or die("Invalid cURL Handle Resouce");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
            curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
            curl_setopt($ch, CURLOPT_NOBODY, !($options['return_body'])); //The content - if true, will not download the contents. There is a ! operation - don't remove it.
            if (isset($options['encoding'])) {
                curl_setopt($ch, CURLOPT_ENCODING, $options['encoding']);
            } // Used if the encoding is gzip.
            if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $url_parts['query']);
            }
            //Set the headers our spiders sends
            curl_setopt($ch, CURLOPT_USERAGENT, $send_header['User-Agent']); //The Name of the UserAgent we will be using ;)
            unset($send_header['User-Agent']);

            $custom_headers = array();
            foreach ($send_header as $key => $value) {
                $custom_headers[] = "$key: $value";
            }
            if (isset($options['modified_since'])) {
                $custom_headers[] = "If-Modified-Since: ".gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since']));
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
            if ($options['referer']) {
                curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
            }

            curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/binget-cookie.txt"); //If ever needed...
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if (isset($url_parts['user']) and isset($url_parts['pass'])) {
                $custom_headers[] = "Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']);
            }

            if ($custom_headers) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
            }
            $response = curl_exec($ch);
            $info = curl_getinfo($ch); //Some information on the fetch

            if ($options['session'] and !$options['session_close']) {
                $GLOBALS['_binget_curl_session'] = $ch;
            } //Dont close the curl session. We may need it later - save it to a global variable
            else {
                curl_close($ch);
            }  //If the session option is not set, close the session.

            //////////////////////////////////////////// FSockOpen //////////////////////////////
        } else { //If there is no curl, use fsocketopen - but keep in mind that most advanced features will be lost with this approch.
            if (isset($url_parts['query'])) {
                if (isset($options['method']) and $options['method'] == 'post') {
                    $page = $url_parts['path'];
                } else {
                    $page = $url_parts['path'] . '?' . $url_parts['query'];
                }
            } else {
                $page = $url_parts['path'];
            }

            if (!isset($url_parts['port'])) {
                $url_parts['port'] = 80;
            }
            $fp = fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, 30);
            if ($fp) {
                $out = '';
                if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query'])) {
                    $out .= "POST $page HTTP/1.1\r\n";
                } else {
                    $out .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1
                }
                $out .= "Host: $url_parts[host]\r\n";
                if (isset($send_header['Accept'])) {
                    $out .= "Accept: $send_header[Accept]\r\n";
                }
                $out .= "User-Agent: {$send_header['User-Agent']}\r\n";
                if (isset($options['modified_since'])) {
                    $out .= "If-Modified-Since: ".gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since'])) ."\r\n";
                }

                $out .= "Connection: Close\r\n";

                //HTTP Basic Authorization support
                if (isset($url_parts['user']) and isset($url_parts['pass'])) {
                    $out .= "Authorization: Basic ".base64_encode($url_parts['user'].':'.$url_parts['pass']) . "\r\n";
                }

                //If the request is post - pass the data in a special way.
                if (isset($options['method']) and $options['method'] == 'post' and $url_parts['query']) {
                    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                    $out .= 'Content-Length: ' . strlen($url_parts['query']) . "\r\n";
                    $out .= "\r\n" . $url_parts['query'];
                }
                $out .= "\r\n";

                fwrite($fp, $out);
                while (!feof($fp)) {
                    $response .= fgets($fp, 128);
                }
                fclose($fp);
            }
        }

        //Get the headers in an associative array
        $headers = array();

        if ($info['http_code'] == 404) {
            $body = "";
            $headers['Status'] = 404;
        } else {
            //Seperate header and content
            $header_text = '';
            $body = $response;
            if (isset($info['header_size'])) {
                $header_text = substr($response, 0, $info['header_size']);
                $body = substr($response, $info['header_size']);
            } else {
                $header_text = reset(explode("\r\n\r\n", trim($response)));
                $body = str_replace($header_text."\r\n\r\n", '', $response);
            }

            // If there is a redirect, there will be multiple headers in the response. We need just the last one.
            $header_parts = explode("\r\n\r\n", trim($header_text));
            $header_text = end($header_parts);

            foreach (explode("\n", $header_text) as $line) {
                $parts = explode(": ", $line);
                if (count($parts) == 2) {
                    $headers[$parts[0]] = chop($parts[1]);
                }
            }

            // :BUGFIX: :UGLY: Some URLs(IMDB has this issue) will do a redirect without the new Location in the header. It will be in the url part of info. If we get such a case, set the header['Location'] as info['url']
            if (!isset($header['Location']) and isset($info['url'])) {
                $header['Location'] = $info['url'];
                $header_text .= "\r\nLocation: $header[Location]";
            }

            $response = $header_text . "\r\n\r\n" . $body;
        }

        if (isset($cache_file)) { //Should we cache the URL?
            file_put_contents($cache_file, $response);
        }

        if ($options['return_info']) {
            return array('headers' => $headers, 'body' => $body, 'info' => $info, 'curl_handle'=>$ch);
        }
        return $body;
    }
}

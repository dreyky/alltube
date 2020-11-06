<?php

namespace Cache;

/**
 * Simple file cache
 *
 * This class is great for those who can't use apc or memcached in their proyects.
 *
 * @author Emilio Cobos (emiliocobos.net) <ecoal95@gmail.com> and github contributors
 * @version 1.0.1
 * @link http://emiliocobos.net/php-cache/
 *
 */
class Cache
{
    /**
     * Configuration
     *
     * @access public
     */
    public static $config = array(
        'cache_path' => 'cache',
        // Default expiration time in *minutes*
        'expires' => 180,
    );

    /**
     * Lets you configure the cache properly, passing an array:
     *
     * <code>
     * Cache::configure(array(
     *   'expires' => 180,
     *   'cache_path' => 'cache'
     * ));
     * </code>
     * Or passing a key/val:
     *
     * <code>
     * Cache::configure('expires', 180);
     * </code>
     *
     * @access public
     * @param mixed $key the array with de configuration or the key as string
     * @param mixed $val the value for the previous key if it was an string
     * @return void
     */
    public static function configure($key, $val = null)
    {
        if (is_array($key)) {
            foreach ($key as $config_name => $config_value) {
                self::$config[$config_name] = $config_value;
            }
        } else {
            self::$config[$key] = $val;
        }
    }

    /**
     * Get a route to the file associated to that key.
     *
     * @access private
     * @param string $key
     * @return string the filename of the php file
     */
    private static function getRoute($key)
    {
        return static::$config['cache_path'] . '/' . md5($key) . '.php';
    }

    /**
     * Get the data associated with a key
     *
     * @access public
     * @param string $key
     * @return mixed the content you put in, or null if expired or not found
     */
    public static function get($key, $raw = false, $custom_time = null)
    {
        if (! self::fileExpired($file = self::getRoute($key), $custom_time)) {
            $content = file_get_contents($file);
            return $raw ? $content : unserialize($content);
        }

        return null;
    }

    /**
     * Put content into the cache
     *
     * @access public
     * @param string $key
     * @param mixed $content the the content you want to store
     * @param bool $raw whether if you want to store raw data or not. If it is true, $content *must* be a string
     *        It can be useful for static html caching.
     * @return bool whether if the operation was successful or not
     */
    public static function put($key, $content, $raw = false)
    {
        $dest_file_name = self::getRoute($key);

        /** Use a unique temporary filename to make writes atomic with rewrite */
        $temp_file_name = str_replace(".php", uniqid("-", true).".php", $dest_file_name);

        $ret = @file_put_contents($temp_file_name, $raw ? $content : serialize($content));

        if ($ret === false) {
            @unlink($temp_file_name);
            return false;
        }

        return @rename($temp_file_name, $dest_file_name);
    }

    /**
     * Delete data from cache
     *
     * @access public
     * @param string $key
     * @return bool true if the data was removed successfully
     */
    public static function delete($key)
    {
        return @unlink(self::getRoute($key));
    }

    /**
     * Flush all cache
     *
     * @access public
     * @return bool always true
     */
    public static function flush()
    {
        $cache_files = glob(self::$config['cache_path'] . '/*.php', GLOB_NOSORT);
        foreach ($cache_files as $file) {
            @unlink($file);
        }
        return true;
    }

    /**
     * Check if a file has expired or not.
     *
     * @access private
     * @param $file the rout to the file
     * @param int $time the number of minutes it was set to expire
     * @return bool if the file has expired or not
     */
    private static function fileExpired($file, $time = null)
    {
        if (! file_exists($file)) {
            return true;
        }
        return (time() > (filemtime($file) + 60 * ($time ? $time : self::$config['expires'])));
    }
}





/*
* JSA VIDEO STREAM CURL
* @autor:     JosAlba
* @web:        jose.alba@jsascript.com
*/

$video         = '';
$video        = '';
$videoTest     = '';
if(isset($_GET['v'])){
    $video = $_GET['v'];
}else{
    echo 'ERROR URL';
    exit();
}


$curlVideo = new VideoStreamCurl($video);

if(isset($_GET['test'])){
  if(isset($_GET['testVideo'])){
    echo 'ERROR VIDEO LOCAL';
    exit();
  }
  /*
  * TEST MODE.
  *
  *
  */
  $videoTest = $_GET['testVideo'];
    $curlVideo->test($videoTest,$_GET['test']);
}else{
  /*
  * VIDEO MODE.
  *
  *
  */
    $curlVideo->start();
}


class VideoStreamCurl{
    private $path = "";
    private $buffer = 102400;
    private $start  = -1;
    private $end    = -1;
    private $size   = 0;
    private $cache    ='';
    private $testMODE = false;
 
    function __construct($filePath){
        $this->path = $filePath;
    }
    
    public function test($localFile,$bytes){
        $this->testMODE = true;
        $b         = array();
        $b['Size']     = $this->curlSize();
        $b['MaxReadBytes']     = $bytes;
        $this->curlDownload(0,($bytes-1));
        $b['BytesCurl']    = $this->cache;
        $a        = fopen($localFile,'rb');
        $b['BytesLocal']    = fread($a, $bytes);
        $b['Header']    = $this->testHeader();
        $b['Start']    = $this->start;
        $b['End']    = $this->end;
        fclose($a);
        echo '<pre>';
            print_r($b);
        echo '</pre>';
    }
    
    private function open(){
        //COMPROBAR SI SE PUEDE ABRIR LA URL.
    
        $ch = curl_init($this->path);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        

        if(curl_exec($ch) === false)
        {
            echo 'ERROR URL';
            exit();
        }

        curl_close($ch);
    }
    
    private function testHeader(){
        ob_get_clean();
        
        $array=array();
            $array[] = 'Content-Type: video/mp4';
            $array[] = 'Cache-Control: max-age=2592000, public';
            $array[] = "Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT';
            $this->start = 0;
            $this->size  = $this->curlSize(); //TAMAÑO DEL ARCHIVO.
            $this->end   = $this->size - 1;
            $array[] = "Accept-Ranges: 0-".$this->end;
            if (isset($_SERVER['HTTP_RANGE'])) {
                $c_start = $this->start;
                $c_end = $this->end;
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                if (strpos($range, ',') !== false) {
                    $array[] = 'HTTP/1.1 416 Requested Range Not Satisfiable';
                    $array[] = "Content-Range: bytes $this->start-$this->end/$this->size";
                    exit;
                }
                if ($range == '-') {
                    $c_start = $this->size - substr($range, 1);
                }else{
                    $range = explode('-', $range);
                    $c_start = $range[0];

                    $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
                }
                $c_end = ($c_end > $this->end) ? $this->end : $c_end;
                if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                    $array[] = 'HTTP/1.1 416 Requested Range Not Satisfiable';
                    $array[] = "Content-Range: bytes $this->start-$this->end/$this->size";
                    exit;
                }
                $this->start = $c_start;
                $this->end = $c_end;
                $length = $this->end - $this->start + 1;
                //fseek($this->stream, $this->start);
                $array[] = 'HTTP/1.1 206 Partial Content';
                $array[] = "Content-Length: ".$length;
                $array[] = "Content-Range: bytes $this->start-$this->end/".$this->size;
            }
        return $array;
    }
    
    private function setHeader(){
        ob_get_clean();
        header("Content-Type: video/mp4");
        header("Cache-Control: max-age=2592000, public");
        header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        
        $this->start = 0;
        $this->size  = $this->curlSize(); //TAMAÑO DEL ARCHIVO.
        $this->end   = $this->size - 1;
        header("Accept-Ranges: 0-".$this->end);
         
        if (isset($_SERVER['HTTP_RANGE'])) {
  
            $c_start = $this->start;
            $c_end = $this->end;
 
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $c_start = $range[0];
                 
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }
            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            //fseek($this->stream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
        }
        else
        {
            header("Content-Length: ".$this->size);
        }  
         
    }
    
    private function curlSize(){
         $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $this->path);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
         curl_setopt($ch, CURLOPT_HEADER, TRUE);
         curl_setopt($ch, CURLOPT_NOBODY, TRUE);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 

         $data = curl_exec($ch);
         $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

         curl_close($ch);
         return $size;
    }
     
    private function curlDownload($start,$end){
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->path);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);     //ENVIAMOS LA PETICION DE EL RANGE QUE QUEREMOS.
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'identity');        //*importante: que sea una copia identica sin comprimir.
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk){
            
            //ESCRITURA DE LOS BYTES.

            if($this->testMODE==true){
                $this->cache = $chunk;
            }else{
                echo $chunk;
            }
            
            return strlen($chunk);            
            
        });
        $result = curl_exec($ch);
        curl_close($ch);
        
    }

    private function stream(){
        /*
        *
        *    CURL, PETICION DE CURL CON LOS BYTES QUE NECESITA.
        *
        */
        
        
        $i = $this->start;
        $e = $this->end;
        set_time_limit(0);
        
        $this->curlDownload($i,$e);
        
    }
     

    function start(){
        $this->open();
        $this->setHeader();
        $this->stream();
    }
}
?>

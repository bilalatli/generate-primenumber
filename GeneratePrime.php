<?php

/**
 * Created by PhpStorm.
 * User: Bilal ATLI
 * Company: GARIVALDI - Digital Solutions
 * E-mail: bilal@garivaldi.com / ytbilalatli@gmail.com
 * Date: 06.08.2018
 * Phone: 0542-433-09-19
 * Time: 16:20
 */
class GeneratePrime
{
    /**
     * Contains Founded Prime Numbers
     *
     * @var array
     */
    private $primes = [];

    /**
     * Configuration Ini File
     *
     * @var string
     */
    private $iniFile = 'primeDataConfiguration.ini';

    /**
     * Default Options
     *
     * @var array
     */
    private $config = [
        'lastPrime'       => 2,
        'primeCount'      => 0,
        'primeBlockSize'  => 5000,
        'primeBlockCount' => 0,
    ];

    /**
     * Block Save Path
     *
     * @var string
     */
    private $blockSaveDirectory = 'prime-blocks/';

    /**
     * GeneratePrime constructor.
     */
    public function __construct()
    {
        if ( !function_exists('gmp_nextprime') )
            die('GMP Nextprime Not Found');

        $this->loadConfig();
    }

    /**
     * Load Configuration File
     *
     * @return null
     */
    private function loadConfig()
    {
        if ( file_exists($this->iniFile) ) {
            $this->config = (object)parse_ini_file($this->iniFile);
        } else {
            $this->createDefaultConfig();
        }
    }

    /**
     * Create Blank Configuration File
     *
     * @return null;
     */
    private function createDefaultConfig()
    {
        $create = touch($this->iniFile);
        if ( $create )
            $this->saveConfig(true);
    }

    /**
     * Save Config Datas in Configuration File
     *
     * @param bool $load Load Configuration File After Save Action
     *
     * @return null
     */
    private function saveConfig( $load = false )
    {
        $data = [];
        foreach ( $this->config as $key => $value )
            $data[] = $key . " = " . $value;

        $data = implode("\r\n", $data);

        $save = $this->fileWrite($this->iniFile, $data);

        if ( $save === true && $load === true )
            $this->loadConfig();
    }

    /**
     * Write any data of any file
     *
     * @param string $filepath
     * @param string $data
     *
     * @return bool
     */
    private function fileWrite( $filepath, $data )
    {
        if ( $fp = fopen($filepath, 'w') ) {
            $startTime = microtime(true);
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if ( !$canWrite ) usleep(round(rand(0, 100) * 1000));
            } while (( !$canWrite ) and ( ( microtime(true) - $startTime ) < 5 ));

            //file was locked so now we can store information
            if ( $canWrite ) {
                fwrite($fp, $data);
                flock($fp, LOCK_UN);
            } else {
                fclose($fp);

                return false;
            }
            fclose($fp);
        }

        return true;
    }

    /**
     * Find next prime number
     *
     * @return int
     */
    public function nextPrime()
    {
        $this->config->lastPrime = gmp_nextprime($this->config->lastPrime);
        $this->config->primeCount++;

        $this->addCollection($this->config->lastPrime);

        return $this->config->lastPrime;
    }

    /**
     * Add Prime Number Into Collection
     *
     * @param int  $prime
     * @param bool $checkAlreadyAdded
     *
     * @return null
     */
    private function addCollection( $prime, $checkAlreadyAdded = false )
    {
        if ( $checkAlreadyAdded === false )
            $this->primes[] = $prime;
        else {
            if ( array_search($prime, $this->primes) == -1 )
                $this->primes[] = $prime;
        }
    }

    /**
     * Generate a block of prime number
     *
     * @param int|null $blockSize Size of Prime Number Block
     *
     * @return array
     */
    public function generateBlock( $blockSize = null )
    {
        $this->resetCollection();

        if ( $blockSize === null )
            $blockSize = $this->config->primeBlockSize;

        $firstPrime = 0;
        $benchmark = [
            'begin'     => microtime(true),
            'end'       => 0,
            'diffrence' => 0,
        ];

        for ( $i = 1; $i <= $blockSize; $i++ ) {
            $this->nextPrime();
            if ( $i === 0 )
                $firstPrime = $this->config->lastPrime;
        }

        $benchmark['end'] = microtime(true);
        $benchmark['diffrence'] = $benchmark['end'] - $benchmark['begin'];

        $filename = $this->saveCollection();

        if ( $filename === false )
            $filename = null;
        else {
            $this->config->primeBlockCount++;
            $this->saveConfig();
        }

        $out = [
            'firstPrime' => $firstPrime,
            'lastPrime'  => $this->config->lastPrime,
            'filename'   => $filename,
            'benchmark'  => $benchmark,
            'itSaved'    => $filename === null ? false : true,
        ];

        return $out;
    }

    /**
     * Save primes collection for file and returns filename if success to write
     *
     * @return mixed
     */
    public function saveCollection()
    {
        $filename = date('Ymd-Hi') . '-' . count($this->primes) . '-' . $this->config->lastPrime . '.data';
        $filepath = $this->blockSaveDirectory . $filename;
        $primeseparator = ';';
        $data = implode($primeseparator, $this->primes);
        $save = $this->fileWrite($filepath, $data);

        return $save ? $filename : false;
    }

    /**
     * Reset Prime Number Collection
     *
     * @return null
     */
    public function resetCollection()
    {
        $this->primes = [];
    }

    /**
     * Get Primes Collection
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->primes;
    }

    /**
     * Get Configuration Object
     *
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }
}
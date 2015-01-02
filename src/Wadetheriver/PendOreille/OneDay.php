<?php
namespace Wadetheriver\PendOreille;

/**
 * Class OneDay Modified for testing and debugging by Wade
 * Use Foundationphp\ namespaced version for full functionality
 *
 * Processes a single day of data from Deep Moor on Lake Pend Oreille.
 *
 * @author David Powers
 * @package Foundationphp\PendOreille
 */
class OneDay {

    /**
     * @var string Date formatted as YYYY_MM_DD
     */
    protected $dateFormat;

    /**
     * @var string Year as four digits
     */
    protected $year;

    /**
     * @var \PDO Database connection
     */
    protected $db;

    /**
     * @var string Name of database table to store data
     */
    protected $table;

    /**
     * @var array Output and error messages
     */
    protected $messages = array();

    /**
     * @var string Base URL for daily data from Deep Moor
     */
    protected $baseUrl = "http://lpo.dt.navy.mil/data/DM/";

    /**
     * established for debugging by wade
     */
    protected $fullUrls = array();

    /**#@+
     * @var string Names of files to be accessed
     */
    protected $air = 'Air_Temp';
    protected $bar = 'Barometric_Press';
    protected $wind = 'Wind_Speed';
    /**#@-*/

    /**
     * @var array Data retrieved from Deep Moor file
     */
    protected $stats = array();

    /**
     * @param \DateTime $date Date of files from which to extract data
     * @param \PDO $db Database connection
     * @param string $table Name of table to insert data into
     */

    public function __construct(\DateTime $date)
    {
        $this->dateFormat = $date->format('Y_m_d');
        $this->year = $date->format('Y');
//        $this->db = $db;
//        $this->table = $table;
        $this->getData('air');
        $this->getData('bar');
        $this->getData('wind');
        $this->processData();
    }

    /**
     * Gets error and other output messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Accesses Deep Moor site and extracts specified type of data
     *
     * @param string $type Type of data to be extracted
     */
    protected function getData($type)
    {
        switch ($type) {
            case 'air':
                $page = $this->air;
                break;
            case 'bar':
                $page = $this->bar;
                break;
            case 'wind':
                $page = $this->wind;
                break;
        }
        // Build the URL for the current date and page
        $url = $this->baseUrl . '/' . $this->year . '/' . $this->dateFormat . '/' . $page;
        $this->fullUrls[] = $url;
        // Access the file and store each line in an array
        // file — Reads entire file into an array
        // @ suppresses PHP warnings if file doesn't exist or can't be opened
        $lines = @ file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Process array of lines
        if ($lines === false) {
            $this->messages[] = "Couldn't get data for $this->dateFormat $page.";
        } else {
            foreach ($lines as $line) {
                // Extract data following the last space in the line

/**  store reading @ time now,not pursuing this further today for DM station
 *   station is down on this day jan1 2015
 */
                $this->stats[$type][] = substr($line, strrpos($line, ' ')+1) . "@" . substr($line, 11, 8); //start at first character of line after space
            }
        }
    }

    /**
     * Inserts data into database table
     */
    protected function processData()
    {
        echo "<pre>";
        echo $this->dateFormat;
        print_r($this->stats);
        echo "</pre>";

        if (isset($this->stats['air'])) {
            $air = implode(',', $this->stats['air']);

            $this->messages[] = $this->fullUrls[0] ." " . $air;
        } else {
            $this->messages[] = "no data to display";
        }

        if (isset($this->stats['air'])) {
            $bar = implode(',', $this->stats['bar']);

            $this->messages[] = $this->fullUrls[1] ." " . $bar;
        } else {
            $this->messages[] = "no data to display";
        }

        if (isset($this->stats['air'])) {
            $wind = implode(',', $this->stats['wind']);

            $this->messages[] = $this->fullUrls[2] ." " . $wind;
        } else {
            $this->messages[] = "no data to display";
        }


    }
} 
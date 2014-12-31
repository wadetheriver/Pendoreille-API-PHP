<?php
namespace MyFoundationphp\PendOreille;

/**
 * Class OneDay
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

    public function __construct(\DateTime $date, \PDO $db, $table)
    {
        $this->dateFormat = $date->format('Y_m_d'); //used in url
        $this->year = $date->format('Y'); //used in url
        $this->db = $db;
        $this->table = $table;
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
        switch ($type) { //constructor runs sequentially through these
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

        // Access the file and store each line in an array
        // @ suppresses PHP warnings if file doesn't exist or can't be opened
        $lines = @ file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Process array of lines
        if ($lines === false) {
            $this->messages[] = "Couldn't get data for $this->dateFormat $page.";
        } else {
            foreach ($lines as $line) {
                // Extract data following the last space in the line
                $this->stats[$type][] = substr($line, strrpos($line, ' ')+1);
            }
        }
    }

    /**
     * Inserts data into database table
     */
    protected function processData()
    {   // See Sandbox version to inspect array data stored in this->stats
        // Create prepared statement
        $sql = "INSERT INTO $this->table (date_recorded, air_temp, bar_press, wind_speed)
                VALUES (:date, :air, :bar, :wind)";
        $statement = $this->db->prepare($sql);

        // Bind values to the named parameters
        // Set value to NULL if no data
        //store entire stats sub arrays as comma separated text
        $statement->bindParam(':date', $this->dateFormat);
        if (isset($this->stats['air'])) {
            $air = implode(',', $this->stats['air']);
            $statement->bindParam(':air', $air); //bindParam accepts only a variable
        } else {
            $statement->bindValue(':air', null, \PDO::PARAM_NULL);
        }
        if (isset($this->stats['bar'])) {
            $bar = implode(',', $this->stats['bar']);
            $statement->bindParam(':bar', $bar);
        } else {
            $statement->bindValue(':bar', null, \PDO::PARAM_NULL);
        }
        if (isset($this->stats['wind'])) {
            $wind = implode(',', $this->stats['wind']);
            $statement->bindParam(':wind', $wind);
        } else {
            $statement->bindValue(':wind', null, \PDO::PARAM_NULL);
        }

        $statement->execute();

        $errorInfo = $statement->errorInfo();
        if (!isset($errorInfo[2])) {
            $this->messages[] = "Data inserted for $this->dateFormat.";
        } else {
            $this->messages[] = $this->dateFormat . ': ' . $errorInfo[2] . '.';
        }

    }
} 
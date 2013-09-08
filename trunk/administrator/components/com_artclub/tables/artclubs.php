<?php

class TableArtclubs extends JTable
{
    var $cf_id = NULL;
    var $uid = NULL;
    var $recordtime = NULL;
    var $ipaddress = NULL;
    var $cf_user_id = NULL;
    var $filename = NULL;
    var $artist = NULL;
    var $title = NULL;
    var $print_date = NULL;
    var $technique = NULL;
    var $dimensions = NULL;
    var $copyright = NULL;
    var $signature = NULL;
    var $signature_location = NULL;
    var $paper_dimensions = NULL;
    var $entry_price = NULL;
    var $num_series = NULL;
    var $work_number = NULL;
    var $collections = NULL;
    var $exhibitions = NULL;
    var $catalogue = NULL;
    var $preservation = NULL;
    var $period = NULL;
    var $holding_gallery = NULL;
    var $category = NULL;
    var $vpn_price = NULL;
    var $exhibition_date = NULL;
    var $approved = 0;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( &$db ) {
        parent::__construct('#__chronoforms_upload', 'cf_id', $db);
    }
}

?>

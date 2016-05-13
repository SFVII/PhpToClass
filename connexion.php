<?php

/**
	brice@nippon.wtf
	Essential Parameters.
 **/

class CONFIG
{

    /***
    	Complete this Parameters. And let's GO :)
    ***/

    const HOST     = 'localhost';
    const DB       = 'YOUR DB NAME'; // Completely USELESS :)
    const USER     = 'YOUR_LOGIN'; // Your Login MySQL
    const PASSWORD = 'YOUR_PASSWORD'; // Your Password MySQL
}


class connexion extends CONFIG
{
    public $key;
    public $qr;
    public function query($query)
    {
        if (isset($query)) {
            $this->key = mysqli_connect(CONFIG::HOST, CONFIG::USER, CONFIG::PASSWORD, CONFIG::DB);
            return mysqli_query($this->key, $query);
        }
    }
}

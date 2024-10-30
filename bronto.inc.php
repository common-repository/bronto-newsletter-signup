<?php
/*
    @Plugin: Bronto Newsletter
    @Version: 2.0.5
    @Author: Scottish Borders Design
    @Author URI: https://scottishbordersdesign.co.uk/
*/
class brontoEmailSender
{
    function testConnection($token){
        $wsdl = "https://api.bronto.com/v4?wsdl";
        $url = "https://api.bronto.com/v4";
        $client = new SoapClient($wsdl, array('trace' => 1, 'encoding' => 'UTF-8'));
        $client->__setLocation($url);
        $token = $token;
        $sessionId = $client->login(array("apiToken" => $token))->return;
        $client->__setSoapHeaders(array(new SoapHeader("http://api.bronto.com/v4", 
                                                       'sessionHeader',
                                                       array('sessionId' => $sessionId))));
        return $sessionId;
    }
    function addContact($email, $listid, $API_TOKEN){
        $client = new SoapClient('https://api.bronto.com/v4?wsdl', array(
            'trace' => 1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        ));
        try {
            $token = $API_TOKEN;
            $sessionId = $client->login(array(
                'apiToken' => $token
            ))->return;
            $session_header = new SoapHeader("http://api.bronto.com/v4", 'sessionHeader', array(
                'sessionId' => $sessionId
            ));
            $client->__setSoapHeaders(array(
                $session_header
            ));
            $contacts = array(
                'email' => $email,
                'listIds' => $listid
            );
            $write_result = $client->addOrUpdateContacts(array(
                $contacts
            ))->return;
            if ($write_result->errors) {
                echo "<div class='error'>{$write_result->results['0']->errorString}</div>";
            } elseif ($write_result->results[0]->isNew == true) {
                echo "<div class='success'>You have been added to the newsletter subscription!</div>";
            } else {
                echo "<div class='success'>You're current subscription has been updated!</div>";
            }
        }
        catch (Exception $e) {
            print "ERROR!\n";
            print_r($e);
        }
    }
    function checkEmail($email, $cemail){
        if (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/", $email)) {
            die("INVALID");
        }
        if ($email != $cemail) {
            die("EMAIL DONT MATCH!");
        }
    }
}
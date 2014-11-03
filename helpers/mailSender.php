<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mailSender
 *
 * @author asus
 */

class mailSender {
    private $message;
    private $to;
    private $subject;
        
    function sendMail(){
        $headers[] = 'From: Intranet Prolub <intranet@prolub.com.co>';
        wp_mail( $this->to, $this->subject, $this->message, $headers );
    }
    
    function PQRAssigned($user, $type, $number, $email){
       global $resource;
       $this->subject = sprintf($resource->getWord("assignedMessageSubject"),$resource->getWord($type),$number);
       $this->message = sprintf($resource->getWord("assignedMessage"),$user, $resource->getWord($type),$number);
       //$this->to = $email;
       $this->to = "adrianotalvaro@hotmail.com";
       $this->sendMail();
    }
    
}

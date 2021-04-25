<?php

namespace App;

/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $message){
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = "Organization: Budżet osobisty";
	$headers[] = "X-Priority: 3";
	$headers[] = 'Content-type: text/html; charset=utf-8';
	$headers[] = 'Content-Transfer-Encoding: 8bit';
	$headers[] = 'From: kontakt@lukaszgorczyk.pl';
	$headers[] = 'Reply-To: kontakt@lukaszgorczyk.pl';
	$headers[] = "Return-Path: kontakt@lukaszgorczyk.pl"; 
	$headers[] = 'X-Mailer: PHP/' . phpversion();
     mail($to, $subject, $message, implode("\r\n", $headers));
	}
}

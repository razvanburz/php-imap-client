<?php

namespace SSilence\ImapClient;


class OutgoingMessage
{

    private $to;
    private $subject;
    private $message;
    private $additional_headers;
    private $cc;
    private $bcc;
    private $rpath;

    /*
     * send()
     */
    private $properties;

    /*
     * createMimeMessage()
     */
    private $envelope;
    private $body;

    /*
     * Send message via imap_mail
     *
     * @return vooid
     */
    public function send()
    {

        $mimeMessage = $this->createMimeMessage();
        $this->message = $mimeMessage;
        $this->preparingSend();
        imap_mail(
            $this->properties->to,
            $this->properties->subject,
            $this->properties->message,
            $this->properties->additional_headers,
            $this->properties->cc,
            $this->properties->bcc,
            $this->properties->rpath
        );
    }

    private function preparingSend(){
        $allowedProperties = [
            'to', 'subject', 'message', 'additional_headers', 'cc', 'bcc', 'rpath'
        ];
        $properties = [
            'to' => $this->to,
            'subject' => $this->subject,
            'message' => $this->message,
            'additional_headers' => $this->additional_headers,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'rpath' => $this->rpath
        ];
        $this->properties = Helper::preparingProperties($properties, $allowedProperties);
    }

    /*
     * http://php.net/manual/ru/function.imap-mail-compose.php
     */
    public function createMimeMessage()
    {
        $this->createBody();

        $envelopeAllowedType = [
            "remail", "return_path", "date", "from", "reply_to", "in_reply_to",
            "subject", "to", "cc", "bcc", "message_id", "custom_headers"
        ];
        /* @var $envelope array */
        $envelope = Helper::preparingProperties($this->envelope, $envelopeAllowedType, Helper::OUT_ARRAY);
        $bodyAllowedType = [
            "type", "encoding", "charset", "type.parameters", "subtype",
            "id", "description", "disposition.type", "disposition", "contents.data",
            "lines", "bytes", "md5"
        ];
        /* @var $body array */
        foreach ($this->body as $key => $part) {
            $this->body[$key] = Helper::preparingProperties($part, $bodyAllowedType, Helper::OUT_ARRAY);
        };
        $body = $this->body;
        return imap_mail_compose ($envelope, $body);
    }

    public function createBody()
    {
        $this->envelope['date'] = '29.03.2017';
        $this->envelope['message_id'] = '81';

        $part1["type"] = TYPEMULTIPART;
        $part1["subtype"] = "mixed";

        $part3["type"] = TYPETEXT;
        $part3["subtype"] = "plain";
        $part3["description"] = "description3";
        $part3["contents.data"] = "contents.data3\n\n\n\t";

        $body[1] = $part1;
        #$body[2] = $part2;
        $body[3] = $part3;

        $this->body = $body;
    }

    public function setAttachment()
    {

    }

    public function setFrom($from)
    {
        $this->envelope['from'] = $from;
        $this->additional_headers = "From: ".$from;
    }

    public function setTo($to)
    {
        $this->to = $to;
        $this->envelope['to'] = $to;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        $this->envelope['subject'] = $subject;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}
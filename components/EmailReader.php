<?php

namespace app\components;
use yii\base\Component;

class EmailReader extends Component {

	// imap server connection
	public $conn;

	// inbox storage and inbox message count
	private $inbox;
	private $msg_cnt;

	// email login credentials
	private $server = 'pop.secureserver.net';
	//private $user   = 'service@shoppylandbyhoney.com';
	private $user   = 'shop@buchoo.com';
	private $pass   = '12345678';
	private $port   = 110;

	// connect to the server and get the inbox emails
	public function __construct() {
		$this->connect();
		$this->inbox();
	}

	// close the server connection
	public function close() {
		$this->inbox = [];
		$this->msg_cnt = 0;

		imap_close($this->conn);
	}

	// open the server connection
	// the imap_open function parameters will need to be changed for the particular server
	// these are laid out to connect to a Dreamhost IMAP server
	public function connect() {
		$this->conn = imap_open('{'.$this->server.':'.$this->port.'/pop3}', $this->user, $this->pass);
	}

	// move the message to a new folder - NOT WORKING WITH POP3
	public function move($msg_index, $folder='INBOX.Processed')
	{
		// move on server
		imap_mail_move($this->conn, $msg_index, $folder);
		imap_expunge($this->conn);

		// re-read the inbox
		$this->inbox();
	}
	
	public function delete($msg_index) {
		// delete from server
		imap_delete($this->conn, $msg_index);
		$check = imap_mailboxmsginfo($this->conn);
var_dump($check);exit;
		imap_expunge($this->conn);

		// re-read the inbox
		$this->close();
	}

	// get a specific message (1 = first email, 2 = second email, etc.)
	public function get($msg_index = NULL) {
		if (count($this->inbox) <= 0)
			return [];
		else if ( ! is_null($msg_index) && isset($this->inbox[$msg_index]))
			return $this->inbox[$msg_index];

		return $this->inbox[0];
	}

	public function getAll()
	{
		if (count($this->inbox) <= 0)
			return [];
		else
			return $this->inbox;
	}
	
	public function getLatest()
	{
		if (count($this->inbox) <= 0)
			return [];
		else
			return $this->inbox[(count($this->inbox)-1)];
	}

	
	private function getdecodevalue($message,$coding)
	{
        switch($coding) {
            case 0:
            case 1:
                $message = imap_8bit($message);
                break;
            case 2:
                $message = imap_binary($message);
                break;
            case 3:
            case 5:
            case 6:
            case 7:
                $message=imap_base64($message);
                break;
            case 4:
                $message = imap_qprint($message);
                break;
        }
        return $message;
	} 
		
	// read the inbox
	public function inbox() {
		$this->msg_cnt = imap_num_msg($this->conn);

		$in = [];
		for($i = 1; $i <= $this->msg_cnt; $i++) 
		{
			$mege = imap_fetchbody($this->conn, $i, 2);
			$structure = imap_fetchstructure($this->conn, $i);
			if(!empty($structure->parts))
			{
				$parts = $structure->parts; 
				if(empty($parts[1]))
					continue;
				
				$part = $parts[1];
				if(!empty($part->disposition) && strtolower($part->disposition) == "attachment") 
				{
					$data = "";
					$filename = \Yii::getAlias('@app')."/web/uploads/attachment/".$part->dparameters[0]->value;
					$data = $this->getdecodevalue($mege, $part->type);
					$fp = fopen($filename, 'w');
					fputs($fp,$data);
					fclose($fp);
				}
			}
		
			$in[] = [
				'index'			=> $i,
				'header'    	=> imap_headerinfo($this->conn, $i),
				'body'      	=> imap_body($this->conn, $i),
				'fetchbody'		=> trim( utf8_encode( quoted_printable_decode( $mege ) ) ),
				'structure' 	=> $structure,
				'attachment' 	=> empty($filename) ? false : $filename
			];
		}

		$this->inbox = $in;
	}
}

?>
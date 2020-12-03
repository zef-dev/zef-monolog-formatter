<?php declare(strict_types=1);

namespace Zef\Monolog;

class MonologFormatter extends \Monolog\Formatter\LineFormatter
{

    private $_requestHash;

	public function __construct()
	{
		parent::__construct( "[%datetime%] %channel%.%level_name%:\t%callee% %message% %context% %extra%\n", null, true, true);
		$this->includeStacktraces();
	}
	
	public function format( array $record) : string {

		$DEPTH		=	4;
		$backtrace	=	debug_backtrace();
		if (isset($backtrace[$DEPTH]['class']) && trim($backtrace[$DEPTH]['class'])) {
			$info = '['.$backtrace[$DEPTH]['class'].':'.$backtrace[$DEPTH]['function'].'('.$backtrace[$DEPTH-1]['line'].")]\t";
		} else {
			$trace		=	$backtrace[$DEPTH-1];
			$info = '['.$trace['file'].'  ('.$trace['line'] .")]\t";
		}

        $record['channel'] = $this->_generateRandomString() . ' ' . $record['channel'];
		$record['callee']	=	$info;
		$record['datetime']	=	date('H:i:s').':'.substr( microtime(), 2, 5);
		
		return parent::format( $record);
	}
	
	// UTIL
    private function _generateRandomString($length = 7) {
            
        if ( !isset( $this->_requestHash)) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $this->_requestHash = '';
            for ($i = 0; $i < $length; $i++) {
                $this->_requestHash .= $characters[rand(0, $charactersLength - 1)];
            }
        }

        return $this->_requestHash;
    }

	public function __toString()
	{
		return get_class( $this).'[]';
	}
}
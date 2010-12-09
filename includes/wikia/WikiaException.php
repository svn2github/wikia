<?php
/**
 * Wikia exception
 *
 * @category Wikia
 * @version $Id:$
 */

/**
 * Wikia exception
 *
 * Base class for Wikia codebase exceptions. PHP 5.3 compatible
 *
 * @category Wikia
 * @author Wojciech Szela <wojtek@wikia-inc.com>
 * @link http://pl2.php.net/manual/en/class.exception.php
 */
class WikiaException extends Exception {
	/**
	 * Previous exception
	 * 
	 * @var Exception|null
	 */
	private $previous;
	
	/**
	 * Constructor
	 *
	 * @link  http://pl2.php.net/manual/en/exception.construct.php
	 * @param string $message
	 * @param int $code
	 * @param Exception $exception
	 */
	public function __construct($message = '', $code = 0, Exception $previous = null) {
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			parent::__construct($message, $code);
			$this->_previous = $previous;
        } else {
			parent::__construct($message, $code, $previous);
        }
	}

	/**
	 * Simulates getPrevious() introduced in PHP 5.3
	 * 
	 * @link   http://pl2.php.net/manual/en/exception.getprevious.php
	 * @param  string $method 
	 * @param  array $args
	 * @return mixed
	 */
    public function __call($method, array $args) {
		if ('getprevious' == strtolower($method)) {
			return $this->_getPrevious();
		}

		return null;
    }

	/**
	 * String representation of the exception
	 *
	 * @link http://pl2.php.net/manual/en/exception.tostring.php
	 * @return string
	 */
	public function __toString() {
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			if (null !== ($e = $this->getPrevious())) {
				return $e->__toString() . "\n\nNext " . parent::__toString();
			}
		}

		return parent::__toString();
    }

	/**
	 * Previous exception getter
	 *
	 * To simulate getPrevious() method introduced in PHP 5.3
	 *
	 * @return Exception|null
	 */
	protected function _getPrevious() {
		return $this->_previous;
	}
}

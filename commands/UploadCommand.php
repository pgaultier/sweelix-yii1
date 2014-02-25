<?php
/**
 * UploadCommand.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.0
 * @link      http://www.sweelix.net
 * @category  commands
 * @package   sweelix.yii1.commands
 */

namespace sweelix\yii1\commands;
use sweelix\yii1\web\UploadedFile;

/**
 * This command browse the xhr/swf upload file and remove
 * old files
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.0
 * @link      http://www.sweelix.net
 * @category  commands
 * @package   sweelix.yii1.commands
 */
class UploadCommand extends \CConsoleCommand {
	public $delay=10;
	private $_checkDate;
	/**
	 * Check files and remove old files
	 * @see CConsoleCommand::run()
	 *
	 * @param $args mixed unused yet, only for compat purpose
	 *
	 * @return void
	 * @since  1.1.0
	 */
    public function run($args) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.commands');
			$targetPath = $targetPath = \Yii::getPathOfAlias(UploadedFile::$targetPath);
			$this->_checkDate = time() - ($this->delay*60);
    		$this->_checkDirectoriesRecursive($targetPath);
    	} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.commands');
			throw $e;
    	}
    }

    /**
     * Browse directories in order to clean up files
     *
     * @param string $path temporary path
     *
     * @return integer
     * @since  1.1.0
     */
    private function _checkDirectoriesRecursive($path) {
		$res = scandir($path);
		$nbFiles = 0;
		foreach($res as $newPath) {
			if(($newPath != '.') && ($newPath != '..')) {
				$newPath = $path.DIRECTORY_SEPARATOR.$newPath;
				if(is_dir($newPath) == true) {
					$nbInsideFiles = $this->_checkDirectoriesRecursive($newPath);
					$nbFiles += $nbInsideFiles;
					if($nbInsideFiles == 0) {
						rmdir($newPath);
					}
				} elseif(is_file($newPath) == true) {
					$nbFiles++;
					$fileTime = filemtime($newPath);
					if($fileTime < $this->_checkDate) {
						if(unlink($newPath) == true) {
							$nbFiles--;
						}
					}
				}
			}
		}
		return $nbFiles;
	}
}
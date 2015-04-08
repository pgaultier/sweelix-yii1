<?php
/**
 * MultiFile.php
 *
 * PHP version 5.3+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2015 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.2.0
 * @link      http://www.sweelix.net
 * @category  caching
 * @package   sweelix.yii1.caching.dependencies
 */

namespace sweelix\yii1\caching\dependencies;

use CCacheDependency;
use CException;

/**
 * Class MultiFile
 *
 * This class check if files have changed
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2015 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.2.0
 * @link      http://www.sweelix.net
 * @category  caching
 * @package   sweelix.yii1.caching.dependencies
 */
class MultiFile extends CCacheDependency
{

    /**
     * @var array list of file to monitor
     */
    public $files;

    /**
     * Constructor
     *
     * @param array $files list of files to monitor
     *
     * @return MultiFile
     * @since  1.9.0
     */
    public function __construct($files = null)
    {
        $this->files = $files;
    }

    /**
     * Generate a simple element which allow to check if cache should be updated
     * (non-PHPdoc)
     * @see CCacheDependency::generateDependentData()
     *
     * @return string
     * @since  1.9.0
     */
    protected function generateDependentData()
    {
        if (($this->files !== null) && (is_array($this->files) === true)) {
            $data = '';
            foreach ($this->files as $file) {
                $data .= filemtime($file);
            }
            return md5($data);
        } else {
            throw new CException('MultiFile.files cannot be empty');
        }
    }
}
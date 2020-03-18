<?php
namespace DAPL\Banner\Controller\Adminhtml\Banner;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('DAPL\Banner\Model\Banner');

            $image = $this->getRequest()->getFiles('image');
            $fileName = ($image && array_key_exists('name', $image)) ? $image['name'] : null;
            if ($image && $fileName) {
                try {
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);  
                    if(array_key_exists('image', $data) && array_key_exists('value', $data['image'])){
                        $bannerPath=$mediaDirectory->getAbsolutePath().$data['image']['value'];
                        if (file_exists($bannerPath)) {
                            unlink($bannerPath);
                        }
                    }
                    /** @var \Magento\Framework\ObjectManagerInterface $uploader */
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
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
                            ->getAbsolutePath('banners')
                    );
                    $data['image'] = 'banners'.$result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            else{
                if(array_key_exists('image', $data) && array_key_exists('delete', $data['image']) && $data['image']['delete']==1){
                    if(array_key_exists('value', $data['image'])){
                        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);  
                        $bannerPath=$mediaDirectory->getAbsolutePath().$data['image']['value'];
                        if (file_exists($bannerPath)) {
                            unlink($bannerPath);
                        }
                        $data['image'] ='';
                    }
                }
                else{
                    $data['image'] =$data['image']['value'];
                }
            }
            
			$id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
			
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Banner Has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}

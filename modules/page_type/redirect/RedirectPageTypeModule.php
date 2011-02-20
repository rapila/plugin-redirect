<?php

class RedirectPageTypeModule extends PageTypeModule {
	
	protected $sLanguageId;
	
	public function __construct(Page $oPage, $sLanguageId = null) {
		parent::__construct($oPage);
		if($sLanguageId == null) {
			$sLanguageId = Session::language();
		}
		$this->sLanguageId = $sLanguageId;
	}
	
	public function display(Template $oTemplate, $bIsPreview = false) {
		$sValue = $this->oPage->getPagePropertyValue('redirect-location-'.$this->sLanguageId, '');
		if(is_numeric($sValue)) {
			$this->oPage = PagePeer::retrieveByPK($sValue);
			LinkUtil::redirect(LinkUtil::link($this->oPage->getFullPathArray()));
		} else if(!$sValue) {
			throw new Exception('Error in RedirectPageTypeModule->display(): no redirect location set');
		}
		LinkUtil::redirect($sValue, false);
	}
	
	public function adminSave($aChosenOptions) {
		ArrayUtil::trimStringsInArray($aChosenOptions);
		$sValue = $aChosenOptions['external'];
		if(!$aChosenOptions['external']) {
			$sValue = $aChosenOptions['internal'];
			PagePeer::retrieveByPK($sValue);
			//TODO: remove outdated references (and when changing page types)
			ReferencePeer::addReference($this, $sValue);
		}
		$this->oPage->updatePageProperty('redirect-location-'.$this->sLanguageId, $sValue);
	}
	
	public function adminLoad() {
		$aResult = array('type' => 'external');
		$aResult['value'] = $this->oPage->getPagePropertyValue('redirect-location-'.$this->sLanguageId, '');
		if(is_numeric($aResult['value'])) {
			$aResult['type'] = 'internal';
		}
		return $aResult;
	}
}
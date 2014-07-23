<?php

class RedirectPageTypeModule extends PageTypeModule {

	public function __construct(Page $oPage = null, NavigationItem $oNavigationItem = null) {
		parent::__construct($oPage, $oNavigationItem);
	}

	public static function doIndex($oNavigationItem) {
		return false;
	}

	public function display(Template $oTemplate, $bIsPreview = false) {
		$sValue = $this->oPage->getPagePropertyValue('redirect-location', '');
		if(is_numeric($sValue)) {
			$this->oPage = PageQuery::create()->findPk($sValue);
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
			//TODO: remove outdated references (and when changing page types)
			ReferencePeer::addReference($this->oPage, PageQuery::create()->findPk($sValue));
		}
		$this->oPage->updatePageProperty('redirect-location', $sValue);
	}

	public function adminLoad() {
		$aResult = array('type' => 'external');
		$aResult['value'] = $this->oPage->getPagePropertyValue('redirect-location', '');
		if(is_numeric($aResult['value'])) {
			$aResult['type'] = 'internal';
		}
		return $aResult;
	}
}

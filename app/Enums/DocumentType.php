<?php 

namespace App\Enums;

enum DocumentType: string {
	case PDF = 'pdf';
	case WEB_PAGE = 'web_page';
	case RAW_TEXT = 'raw_text';

	public static function getSelectList()
	{
		return [
			['value' => self::PDF->value, 'label' => 'PDF'],
			['value' => self::WEB_PAGE->value, 'label' => 'Web Page'],
			['value' => self::RAW_TEXT->value, 'label' => 'Free Text'],
		];		
	}
}
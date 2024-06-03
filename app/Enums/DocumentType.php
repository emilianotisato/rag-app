<?php 

namespace App\Enums;

enum DocumentType: string {
	case PDF = 'pdf';
	case WEB_PAGE = 'web_page';
	case RAW_TEXT = 'raw_text';
}
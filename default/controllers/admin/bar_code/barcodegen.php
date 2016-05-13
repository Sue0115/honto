<?php
class barcodegen extends Admin_Controller{
	/**
	 * 打印条码
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUrl($file_name)
	{
		return $this->bar_url = APPPATH.'third_party/barcodegen/class/' . $file_name . '.php';
	}
	
	public function image()
	{
		$get = $this->input->get();
		
		if(isset($_GET['code']) && isset($_GET['t']) && isset($_GET['r']) && isset($_GET['text']) && isset($_GET['f1']) && isset($_GET['f2']) && isset($_GET['o']) && isset($_GET['a1']) && isset($_GET['a2']))
		{
		
			$BCGColor	= $this->getUrl('BCGColor');
			$BCGBarcode	= $this->getUrl('BCGBarcode');
			$BCGDrawing	= $this->getUrl('BCGDrawing');
			$BCGFontFile= $this->getUrl('BCGFontFile');
			$the_custom	= $this->getUrl('BCG' . $_GET['code'] . '.barcode');
			
			if (file_exists($BCGColor) && file_exists($BCGBarcode) && file_exists($BCGDrawing) && file_exists($BCGFontFile) && file_exists($the_custom))
			{
				require_once $BCGColor;
				require_once $BCGBarcode;
				require_once $BCGDrawing;
				require_once $BCGFontFile;
				require_once $the_custom;
				
				if($_GET['f1'] !== '0' && $_GET['f1'] !== '-1' && intval($_GET['f2']) >= 1)
				{
					$font			= new BCGFontFile(APPPATH.'third_party/barcodegen/class/font/' .$_GET['f1'], intval($_GET['f2']));
				}
				else{
					$font = 0;
				}
				
					$color_black	= new BCGColor(0, 0, 0);
					$color_white	= new BCGColor(255, 255, 255);
					$codebar = 'BCG'.$_GET['code'];
					$code_generated	= new $codebar();
					
					if($_GET['code']=='code128'){
						if(isset($_GET['a1']) && intval($_GET['a1']) === 1) {
							$code_generated->setChecksum(true);
						}
						if(isset($_GET['a2']) && !empty($_GET['a2'])) {
							$code_generated->setStart($_GET['a2']);
						}
						if(isset($_GET['a3']) && !empty($_GET['a3'])) {
							$code_generated->setLabel($_GET['a3']);
						}
					}
					
					$code_generated->setThickness($_GET['t']);
					$code_generated->setScale($_GET['r']);
					$code_generated->setBackgroundColor($color_white);
					$code_generated->setForegroundColor($color_black);
					$code_generated->setFont($font);
					$code_generated->parse($_GET['text']);
					$drawing = new BCGDrawing('', $color_white);
					$drawing->setBarcode($code_generated);
					$drawing->draw();
					
					if(intval($_GET['o']) === 1)
					{
						header('Content-Type: image/png');
					}
					elseif(intval($_GET['o']) === 2)
					{
						header('Content-Type: image/jpeg');
					}
					elseif(intval($_GET['o']) === 3)
					{
						header('Content-Type: image/gif');
					}
					
					$drawing->finish(intval($_GET['o']));
			}
			else
			{
				header('Content-Type: image/png');
				readfile(APPPATH.'third_party/barcodegen/html/error.png');
			}
		}
		else
		{
			header('Content-Type: image/png');
			readfile(APPPATH.'third_party/barcodegen/html/error.png');
		}
	}
}
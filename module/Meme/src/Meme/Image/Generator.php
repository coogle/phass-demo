<?php

namespace Meme\Image;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Generator implements FactoryInterface
{
	/**
	 * @var int
	 */
	protected $_width;
	
	/**
	 * @var int
	 */
	protected $_height;
	
	/**
	 * @var string
	 */
	protected $_fontFile;
	
	/**
	 * @var int
	 */
	protected $_fontMinSize;
	
	
	/**
	 * @var int
	 */
	protected $_fontMaxSize;
	
	/**
	 * @var int
	 */
	protected $_strokeWidth;
	
	/**
	 * @var string
	 */
	protected $_fontColor;
	
	/**
	 * @var string
	 */
	protected $_strokeColor;
	
	/**
	 * @return the $_width
	 */
	public function getWidth() {
		return $this->_width;
	}

	/**
	 * @return the $_height
	 */
	public function getHeight() {
		return $this->_height;
	}

	/**
	 * @param number $_width
	 * @return self
	 */
	public function setWidth($_width) {
		$this->_width = $_width;
		return $this;
	}

	/**
	 * @param number $_height
	 * @return self
	 */
	public function setHeight($_height) {
		$this->_height = $_height;
		return $this;
	}

	/**
	 * @return the $_fontFile
	 */
	public function getFontFile() {
		return $this->_fontFile;
	}

	/**
	 * @return the $_fontSize
	 */
	public function getMinFontSize() {
		return $this->_fontMinSize;
	}

	public function getMaxFontSize() {
		return $this->_fontMaxSize;
	}
	
	/**
	 * @return the $_strokeWidth
	 */
	public function getStrokeWidth() {
		return $this->_strokeWidth;
	}

	/**
	 * @return the $_fontColor
	 */
	public function getFontColor() {
		return $this->_fontColor;
	}

	/**
	 * @return the $_strokeColor
	 */
	public function getStrokeColor() {
		return $this->_strokeColor;
	}

	/**
	 * @param string $_fontFile
	 * @return self
	 */
	public function setFontFile($_fontFile) {
		$this->_fontFile = $_fontFile;
		return $this;
	}

	/**
	 * @param string $_fontSize
	 * @return self
	 */
	public function setMinFontSize($_fontSize) {
		$this->_fontMinSize = $_fontSize;
		return $this;
	}

	/**
	 * @param int $_fontSize
	 * @return self
	 */
	public function setMaxFontSize($_fontSize) {
		$this->_fontMaxSize = $_fontSize;
		return $this;
	}
	
	/**
	 * @param number $_strokeWidth
	 * @return self
	 */
	public function setStrokeWidth($_strokeWidth) {
		$this->_strokeWidth = $_strokeWidth;
		return $this;
	}

	/**
	 * @param string $_fontColor
	 * @return self
	 */
	public function setFontColor($_fontColor) {
		$this->_fontColor = $_fontColor;
		return $this;
	}

	/**
	 * @param string $_strokeColor
	 * @return self
	 */
	public function setStrokeColor($_strokeColor) {
		$this->_strokeColor = $_strokeColor;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @return self
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = $serviceLocator->get('Config');
		
		$config = $config['meme'];
		
		$generator = new Generator();
		
		$generator->setFontColor($config['default_font_color'])
				  ->setMaxFontSize($config['default_max_font_size'])
				  ->setMinFontSize($config['default_min_font_size'])
				  ->setFontFile(realpath($config['font_file']))
				  ->setStrokeColor($config['default_stroke_color'])
				  ->setStrokeWidth($config['default_stroke_width'])
				  ->setWidth($config['default_width'])
				  ->setHeight($config['default_height']);
		
		return $generator;
	}
	
	public function createFromImageString($imgData, $textStr)
	{
		$textStr = strtoupper($textStr);
		
		$imagic = new \Imagick();
		$imagic->readImageBlob($imgData);
		
		$strokePixel = new \ImagickPixel($this->getStrokeColor());
		$fontPixel = new \ImagickPixel($this->getFontColor());
		
		$imageSize = $imagic->getImageGeometry();
		
		$maxWidth = (int)$imageSize['width'] * 0.94;
		
		$text = new \ImagickDraw();
		
		$text->setFont($this->getFontFile());
		$text->setFontSize($this->getMaxFontSize());
		$text->setFillColor($this->getFontColor());
		$text->setStrokeColor('#000');
		$text->setStrokeWidth(2);
		$text->setStrokeAntiAlias(true);
		$text->setTextAntiAlias(true);
		$text->setGravity(\Imagick::GRAVITY_NORTH);
		
		$fontMetrics = $imagic->queryFontMetrics($text, $textStr);
		
		$fontRatio = $this->getMaxFontSize() / $this->getMinFontSize();
		$textRatio = $fontMetrics['textWidth'] / $maxWidth;
		
		if($textRatio > $fontRatio) {
			$wordWrapLen = (int)strlen($textStr) * .55;
			
			$textStr = wordwrap($textStr, $wordWrapLen, "\n", true);
			
			$maxFont = floor($this->getMaxFontSize() * .6);
			
			$text->setfontsize($maxFont);
			
			$fontMetrics = $imagic->queryFontMetrics($text, $textStr);
		}
		
		$textRatio = $fontMetrics['textWidth'] / $maxWidth;
		
		if($textRatio > 1) {
			$this->setMaxFontSize(floor($this->getMaxFontSize() * (1/$textRatio)));
			$text->setFontSize($this->getMaxFontSize());
		}
		
		$imagic->annotateImage($text, 0, 5, 0, $textStr);
		
		$text->setFillColor($this->getFontColor());
		$text->setStrokeColor($this->getStrokeColor());
		
		$imagic->annotateImage($text, 0, 5, 0, $textStr);
		return (string)$imagic;
	}
}
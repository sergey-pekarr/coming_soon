<?php

class GoogleMapProjection     
{         
	private  $PixelTileSize = 256;
	private  $DegreesToRadiansRatio;    
	private  $RadiansToDegreesRatio;   
	private  $PixelGlobeCenter;//point 
	private  $XPixelsToDegreesRatio;     
	private  $YPixelsToRadiansRatio;
	
	public function __construct($zoomLevel, $pixelTileSize = 256)         
	{          
		$this->PixelTileSize = $pixelTileSize;
		$this->DegreesToRadiansRatio = 180/pi();
		$this->RadiansToDegreesRatio = pi()/180;
		$this->PixelGlobeCenter = array('X' => 0, 'Y' => 0);
		
		$pixelGlobeSize = $this->PixelTileSize * pow(2, $zoomLevel);             
		$this->XPixelsToDegreesRatio = $pixelGlobeSize / 360;             
		$this->YPixelsToRadiansRatio = $pixelGlobeSize / (2 * pi());             
		$halfPixelGlobeSize = $pixelGlobeSize / 2;             
		$this->PixelGlobeCenter = array('X' => $halfPixelGlobeSize, 'Y' => $halfPixelGlobeSize);        
	}         
	public function coordinatesToPixel($coordinates)//point -> point  
	{             
		$x = round($this->PixelGlobeCenter['X'] + ($coordinates['X'] * $this->XPixelsToDegreesRatio));             
		$f = min(max(sin($coordinates['Y'] * $this->RadiansToDegreesRatio), -0.9999), 0.9999);             
		$y = round($this->PixelGlobeCenter['Y'] + .5 * log((1 + $f) / (1 - $f)) * -$this->YPixelsToRadiansRatio);             
		return array('X' => $x, 'Y' => $y);         
	}          
	
	public function pixelToCoordinates($pixel)//point -> point          
	{             
		$longitude = ($pixel['X'] - $this->PixelGlobeCenter['X']) / $this->XPixelsToDegreesRatio;             
		$latitude = (2 * atan(exp(($pixel['Y'] - $this->PixelGlobeCenter['Y']) / -$this->YPixelsToRadiansRatio)) - pi() / 2) 
					* $this->DegreesToRadiansRatio;             
		return array('X' => $longitude, 'Y' => $latitude);         
	}            
} 
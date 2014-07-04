<?php 
/**
* @version		$Id: aiolos.class.php 818 2012-01-05 13:20:54Z webgift $
* @package		Elxis CMS
* @subpackage	Templates / Aiolos
* @author		Stavros Stratakis ( http://www.webgiftgr.com )
* @copyright	(c) 2009-2012 Webgift web services (http://www.webgiftgr.com). All rights reserved.
* @license		Creative Commons 3.0 Attribution-ShareAlike Unported ( http://creativecommons.org/licenses/by-sa/3.0/ )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
**************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');

class tplaiolos {
    private $tplparams = array();
    private $css = array();
    private $url = '';
    
    
	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/    
    public function __construct() {
        $this->url = eFactory::getElxis()->secureBase().'/templates/aiolos';
        $this->getParams();
    }


	/***************************/
	/* GET TEMPLATE PARAMETERS */
	/***************************/    
    private function getParams() {
        $eModule = eFactory::getModule();
        elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
        
        $xmlpath = ELXIS_PATH.'/templates/aiolos/aiolos.xml';
        $tplparams = $this->getDBParams();
        
        $params = new  elxisParameters($tplparams, $xmlpath, 'template');
        
        $this->tplparams['bgcolor'] = $params->get('bgcolor', 'ochre');
        $this->tplparams['width'] = (int)$params->get('width', 980);
        $this->tplparams['columns'] = $params->get('columns', 3);
        $this->tplparams['elxpath'] = (int) $params->get('elxpath', 1);
        $this->tplparams['selogo'] = $params->get('selogo', 'elogo.png');
        $this->tplparams['logo_width'] = (int)$params->get('logo_width', 202);
        $this->tplparams['logo_height'] = (int)$params->get('logo_height', 63);
        $this->tplparams['align'] = $params->get('align', 'center');
        $this->tplparams['col_width'] = (int)$params->get('col_width', 180);
        $this->tplparams['sel_col'] = $params->get('sel_col', 'left');
        unset($params); //free memory
        
        $countright = $eModule->countModules('right'); // Can be added more module positions + countModules('user3')
        $countleft = $eModule->countModules('left'); 
        unset($eModule);
        
        //Checkings
        $this->tplparams['logo_width'] = ($this->tplparams['logo_width'] < 100 || $this->tplparams['logo_width'] >= 300) ? 300 : $this->tplparams['logo_width'];
        $this->tplparams['logo_height'] = ($this->tplparams['logo_height'] < 35 || $this->tplparams['logo_height'] >= 75) ? 75 : $this->tplparams['logo_height'];
        
        $this->resizeColmns($countleft, $countright); 
    }
    
    
	/*******************/
	/* CHANGE LOGOTYPE */
	/*******************/       
    public function changeLogo() {
        $elxis = eFactory::getElxis();
        if(file_exists(ELXIS_PATH.'/'.$this->tplparams['selogo'])) {
            $imgsrc = $elxis->secureBase().'/'.$this->tplparams['selogo'];
        } else {
            $imgsrc = $this->url.'/images/elogo.png';
        }
        $imgalign = (eFactory::getLang()->getinfo('RTLSFX') == '-rtl') ? 'right' : 'left';
                
        echo '<img src="'.$imgsrc.'" align="'.$imgalign.'" alt="'.$elxis->getConfig('SITENAME').'" width="'.$this->tplparams['logo_width'].'" height="'.$this->tplparams['logo_height'].'" />'."\n";
        
    }
    
    
	/***************************/
	/* GET TEMPLATE PARAMETERS */
	/***************************/    
    private function resizeColmns($countleft, $countright) {
        $eModule = eFactory::getModule();
        
        $symbol = ($this->tplparams['width'] != 100) ? 'px' : '%';
        $this->tplparams['col_width'] = ($this->tplparams['col_width'] <= 150 && $this->tplparams['col_width'] > 250) ? 150 : $this->tplparams['col_width']; // Set min columns width as 150px and max width 250px
        $this->css[] = ($this->tplparams['elxpath'] == 0) ? '.elx_pathway {display:none;}' : ''; 
        
        $cfooter = $eModule->countModules('user1') + $eModule->countModules('user2') + $eModule->countModules('user3') + $eModule->countModules('user4');
        $ctop = $eModule->countModules('menu');
        if (!$ctop) {
        	$this->css[] = '.header-bottom {background:none;box-shadow:none; }';
        }
        if(!$cfooter) {
            $this->css[] = '.footer-wrapper{display:none; visibility:hidden;}';
            $this->css[] = '.bottom-area{min-height:100px;}';
        } 
        if($this->tplparams['align'] == 'center') {
            $this->css[] = '.total-wrapper, .fixed_width, .header-wrapper{margin:0 auto;}';
        } else if ($this->tplparams['align'] == 'left') {
            $this->css[] = '.total-wrapper, .fixed_width {float:left;}';
        }else if ($this->tplparams['align'] == 'right') {
            $this->css[] = '.total-wrapper, .fixed_width, .header-wrapper{float:right;}';
        }
        $this->css[] = ($this->tplparams['width'] == 100) ? '.total-wrapper{overflow:hidden;}' : '';
        if($cfooter){
            for($i=0; $i<=4; $i++) {
                if ($this->tplparams['width'] == 100) {
                    $this->css[] = 'div.content_user'.$i.'{text-align:center;}';
                    $this->css[] = 'div.content_user'.$i.' p{text-align:center;}';
                }
                $this->css[] = 'div.content_user'.$i.' div.module ul.elx_vmenu {margin:0 auto;}';
            }
        }
        $this->css[] = '.pre-header {background: url('.$this->url.'/images/headerbg.png) 0 0 repeat;}';
        $this->css[] = ($this->tplparams['elxpath']) ? 'div.leftcolumn div.module h3, div.rightcolumn div.module h3 {margin:0px 0 3px 0;}': ' div.leftcolumn div.module h3, div.rightcolumn div.module h3 {margin:0px 0 3px 0;}' ;
        if($this->tplparams['bgcolor'] == 'white') { 
            $this->css[] = 'body,.wrapper{background:#ffffff;}';
            $this->css[] = '.total-wrapper{background:#ffffff;width:'.$this->tplparams['width'].$symbol.';border-right:1px solid #ccc;border-left:1px solid #ccc;}'; 
             $this->css[] = '.wrapper{background:#fefefe;}'; 
        } elseif ($this->tplparams['bgcolor'] == 'grey')  { 
            $this->css[] = '.total-wrapper{background: #fff; width:'.$this->tplparams['width'].$symbol.';border-right:1px solid #ccc;border-left:1px solid #ccc;}';
            $this->css[] = '.content-wrapper{background: #fff;}';
        } elseif ($this->tplparams['bgcolor'] == 'lightgreen') {
            $this->css[] = '.total-wrapper{background: #fff;width:'.$this->tplparams['width'].$symbol.';border-right:1px solid #ccc;border-left:1px solid #ccc;}';
            $this->css[] = '.content-wrapper{background: #fff;}';
            $this->css[] = '.wrapper{background:#ECF1EB url('.$this->url.'/images/lgreen.png) 0 0 repeat-x;}';
        } elseif ($this->tplparams['bgcolor'] == 'blue') {
            $this->css[] = '.total-wrapper{background: #fff;width:'.$this->tplparams['width'].$symbol.';border-right:1px solid #ccc;border-left:1px solid #ccc;}';
            $this->css[] = '.content-wrapper{background: #fff;}';
            $this->css[] = '.wrapper{background:#fff url('.$this->url.'/images/lblue.png) 0 0 repeat-x;}'; //B0CDDE 
        } elseif ($this->tplparams['bgcolor'] == 'ochre') {
            $this->css[] = '.total-wrapper{background: #fff;width:'.$this->tplparams['width'].$symbol.';border-right:1px solid #bbb;border-left:1px solid #bbb;}';
            $this->css[] = '.content-wrapper{background: #fff;}';
            $this->css[] = '.wrapper{background:#fff url('.$this->url.'/images/lochre.png) 0 0 repeat-x;}';             
        }
        
        if ($this->tplparams['columns'] == 3) {
            if($countleft > 0 && $countright > 0) {
                $mc = ($this->tplparams['width'] != 100) ? $this->tplparams['width'] - 2*$this->tplparams['col_width'] - 15 : 0;
                
                if(!$mc) {
                    $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'%;}';
                    $this->css[] = '.leftcolumn, .rightcolumn {width:20%;}';
                    $this->css[] = '.maincontent{width:78%;}';
                    $this->css[] = '.main-body{width:79%;}';
                    $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'%; margin:0;}';
                } else {
                    $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'px;}';
                    $this->css[] = '.leftcolumn, .rightcolumn{width:'.$this->tplparams['col_width'].'px;}';
                    $this->css[] = '.maincontent{width:'.$mc.'px;}';
                    $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'px; }';
                }
            
            }
            
            if(($countleft == 0 && $countright > 0) || ($countleft > 0 && $countright == 0)) {
                $mc = ($this->tplparams['width'] != 100) ? $this->tplparams['width'] - $this->tplparams['col_width'] - 15 : 0;
                
                if(!$mc) {
                    $ml = ($countleft == 0 && $countright > 0) ? 'display:none; visibility: hidden;' : 'width:20%;';
                    $mr = ($countleft > 0 && $countright == 0) ? 'display:none; visibility: hidden;' : 'width:20%;';
                    
                    $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'%;}';
                    $this->css[] = '.leftcolumn{'.$ml.'}';
                    $this->css[] = '.rightcolumn {'.$mr.'}';
                    $this->css[] = '.maincontent{width:78%;}';
                    $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'%; margin:0;}';
                } else {
                    $ml = ($countleft == 0 && $countright > 0) ? 'display:none; visibility: hidden;' : 'width:'.$this->tplparams['col_width'].'px;';
                    $mr = ($countleft > 0 && $countright == 0) ? 'display:none; visibility: hidden;' : 'width:'.$this->tplparams['col_width'].'px;';
                    
                    $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'px;}';
                    $this->css[] = '.leftcolumn{'.$ml.'}';
                    $this->css[] = '.rightcolumn {'.$mr.'}';
                    $this->css[] = '.maincontent{width:'.$mc.'px;}';
                    $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'px;}';
                }
            } 
                                   
        }
        
        if($this->tplparams['columns'] == 2) {
            $mc = ($this->tplparams['width'] != 100) ? $this->tplparams['width'] - $this->tplparams['col_width'] - 15 : 0;
            
            if(!$mc) {
                
                if($this->tplparams['sel_col'] == 'left' && $countleft > 0) {
                    $ml = 'display:none; visibility:hidden;';
                } else {
                    $ml = 'width:20%;';
                    $this->css[] = '.maincontent{width:99%;}';
                    $this->css[] = '.main-body{width:79%;}';
                }
                
                if($this->tplparams['sel_col'] == 'right' && $countright > 0) {
                    $mr = 'display:none; visibility:hidden;';
                } else {
                    $mr = 'width:20%;';
                    $this->css[] = '.maincontent{width:79%;}';
                    $this->css[] = '.main-body{width:99%;}';
                }

                $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'%;}';
                $this->css[] = '.leftcolumn{'.$mr.'}';
                $this->css[] = '.rightcolumn {'.$ml.'}';
                $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'%; margin:0;}';                
            } else {

                $ml = ($this->tplparams['sel_col'] == 'left' && $countleft > 0) ? 'display:none; visibility:hidden;' : 'width:'.$this->tplparams['col_width'].'px;';
                $mr = ($this->tplparams['sel_col'] == 'right' && $countright > 0) ? 'display:none; visibility:hidden;' : 'width:'.$this->tplparams['col_width'].'px;';
                                
                $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].'px;}';
                $this->css[] = '.leftcolumn{'.$mr.'}';
                $this->css[] = '.rightcolumn {'.$ml.'}';
                $this->css[] = '.maincontent{width:'.$mc.'px;}';
                $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].'px; margin:0 auto;}';                          
            }


        }
        
        if(($countleft == 0 && $countright == 0 && $this->tplparams['columns'] == 3) || ($this->tplparams['columns'] == 1)) {
            $mc = ($this->tplparams['width'] != 100) ? $this->tplparams['width'] -10 : $this->tplparams['width'] - 1;
            
            $this->css[] = '.content-wrapper{width:'.$this->tplparams['width'].$symbol.';}';
            $this->css[] = '.leftcolumn, .rightcolumn{display:none; visibility: hidden;}';
            $this->css[] = '.maincontent{width:'.$mc.$symbol.';}';
            $this->css[] = '.fixed_width, .header-wrapper{width:'.$this->tplparams['width'].$symbol.'; margin:0 auto;}';                
        }         

    }
    
    
	/****************************/
	/* GET HIDDEN WEBSITE'S URL */
	/****************************/         
    public function hiddendiv() {
        return '<div id="hiddenurl" style="display:none; visibility: hidden;">'.$this->url.'</div>'."\n";
    }


	/*********************/
	/* SHOW FOOTER CODE */
	/********************/     
    public function footer() {
        echo 'Powered by <a href="http://www.elxis.org" title="Elxis CMS">Elxis</a> - Open Source CMS';
        echo ' - Designed by <strong><a href="http://www.webgiftgr.com/" title="Webgift web services">Webgift web services</a></strong>';
    }
    
    
	/****************************/
	/* LOAD CSS - JS ON HEADER  */
	/****************************/     
    public function loadHeader() {
        
		if (count($this->css) > 0) {
			echo "\n".'<style type="text/css">'."\n";
			foreach ($this->css as $rule) {
				echo $rule."\n";
			}
			echo "</style>\n";   
        }
        echo '<link rel="stylesheet" href="'.eFactory::getElxis()->secureBase().'/templates/aiolos/css/'.$this->tplparams['bgcolor'].'.css" type="text/css" />'."\n";
        echo '<script type="text/javascript" src="'.eFactory::getElxis()->secureBase().'/templates/aiolos/includes/aiolos.js"></script>'."\n";          
    }    
    
    
	/***********************************/
	/* GET TEMPLATE PARAMETERS FROM DB */
	/***********************************/    
    private function getDBParams() {
        $db = eFactory::getDB();
        
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__templates')
		."\n WHERE ".$db->quoteId('template')." = ".$db->quote('aiolos')
        ."\n AND ".$db->quoteId('section')." = ".$db->quote('frontend');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
        
		return (string)$stmt->fetchResult();        
    }
    
}
?>
<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2012
 * @package    agentSelection
 * @license    GNU/GPL 2
 * @filesource
 */

/**
 * Class AgentSelection
 */
class AgentSelection
{

    protected static $_arrIOs = array('iPad', 'iPhone', 'iPod');
    
    /**
     * Initialize the object
     */
    public function __construct()
    {
        if (version_compare(VERSION, '2.11', '<'))
        {
            require_once TL_ROOT . '/system/config/agents.php';
        }
    }    

    /**
     * Check if the operation system has permission
     * 
     * @param mixed $mixedConfig
     * @param stdClass $objUa
     * @return boolean 
     */
    public static function checkOsPermission($mixedConfig, $objUa)
    {
        $arrIOs = array('iPad', 'iPhone', 'iPod');

        if ($mixedConfig != '')
        {
            if ($mixedConfig['config']['os'] == $objUa->os)
            {

                if (in_array($mixedConfig['value'], self::$_arrIOs))
                {
                    if (strpos($objUa->string, $mixedConfig['value']) !== false)
                    {
                        return true;
                    }
                }
                else
                {

                    return true;
                }
            }
            return false;
        }
        
        return true;
    }
    
    public function getOsClass()
    {
        $objUa = Environment::getInstance()->agent;
        
        $arrIOs = self::$_arrIOs;
        
        foreach($arrIOs as $strIOs)
        {
            if (strpos($objUa->string, $strIOs) !== false)
            {
                return strtolower($strIOs);
            }
        }
        
        return '';
    }
    
    /**
     * Check if the browser version has permission
     * 
     * @param mixed $mixedConfig
     * @param stdClass $objUa
     * @param string $strOperator
     * @return boolean
     */
    public static function checkBrowserVerPermission($mixedConfig, $objUa, $strOperator)
    {
        if($mixedConfig != '')
        {
            switch ($strOperator)
            {
                case 'lt':
                    if($objUa->version < $mixedConfig) return true;
                    break;
                case 'lte':
                    if($objUa->version <= $mixedConfig) return true;
                    break;
                case 'gte':
                    if($objUa->version >= $mixedConfig) return true;
                    break;
                case 'gt':
                    if($objUa->version > $mixedConfig) return true;
                    break;
                default:
                    if($objUa->version == $mixedConfig) return true;
                    break;
            }            
            return false;
        }

        return true;
    }

    /**
     * Return option array for operation systems
     * 
     * @return array
     */
    public function getClientOs()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['os'] as $strLabel => $arrOs)
        {
            $arrOptions[$strLabel] = $strLabel;
        }

        return $arrOptions;
    }

    /**
     * Return browser array for operation systems
     * 
     * @return array
     */
    public function getClientBrowser()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['browser'] as $strLabel => $arrBrowser)
        {
            $arrOptions[$strLabel] = $strLabel;
        }

        return $arrOptions;
    }

//    /**
//     * Replace insert tags with their values
//     * @param string
//     * @param boolean
//     * @return string
//     */
//    public function replaceInsertTags($strBuffer)
//    { 
//        $tags = preg_split('/\{\{([^\}]+)\}\}/', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
//
//        $strBuffer = '';
//        $arrCache  = array();
//
//        for ($_rit = 0; $_rit < count($tags); $_rit = $_rit + 2)
//        {
//            $strBuffer .= $tags[$_rit];
//            $strTag = $tags[$_rit + 1];
//
//            // Skip empty tags
//            if ($strTag == '')
//            {
//                continue;
//}
//
//            // Load value from cache array
//            if (isset($arrCache[$strTag]))
//            {
//                $strBuffer .= $arrCache[$strTag];
//                continue;
//            }
//
//            $elements = explode('::', $strTag);
//
//            $arrCache[$strTag] = '';
//
//            // Replace the tag
//            switch (strtolower($elements[0]))
//            {
//                case 'ifos':
//                    $arrOs = array();
//                    foreach($GLOBALS['TL_CONFIG']['os'] as $strSystem => $arrSystem)
//                    {
//                        $arrOs[] = standardize($strSystem);
//                    }                                       
//                    $objUa = $this->Environment->agent;
//                    
//                    if ($elements[1] != '' && $elements[1] != $objPage->language)
//                    {
////                        for ($_rit; $_rit < count($tags); $_rit+=2)
////                        {
////                            if ($tags[$_rit + 1] == 'iflng')
////                            {
////                                break;
////                            }
////                        }
//                    }
//                    unset($arrCache[$strTag]);
//                    break;
//            }
//
//            $strBuffer .= $arrCache[$strTag];
//        }
//
//        return $strBuffer;
//    }
//
//    public function parseFrontendTemplate($strContent, $strTemplate)
//    {
//        $arrInsertTags = array('ifos', 'ifbrowser', 'ifmobile');
//
//        $strPattern = '/{{([^}}]*)}/';
//        preg_match_all($strPattern, $strContent, $arrMatches);
//
//        foreach ($arrInsertTags as $strInsertTag)
//        {
//            if (in_array($strInsertTag, $arrMatches[1]))
//            {
//                return $this->replaceInsertTags($strContent);
//                break;
//            }
//        }
//
//        return $strContent;
//    }
    
    public function generatePage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular)
    {
        $objPage->cssClass .= $this->getOsClass();
    }

}

?>
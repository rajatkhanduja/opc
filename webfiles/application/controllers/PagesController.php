<?php
/**
 * Copyright 2007-2009 Chennai Mathematical Institute
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @file   PagesController.php
 * @author Arnold Noronha <arnold@cmi.ac.in>
 */

require_once "lib/contest.inc";

class PagesController extends Zend_Controller_Action
{
	public function indexAction() 
	{
		$page = $this->getRequest()->get("page") ;
		$contest = Contest::factory(webconfig::getContestId());

		if (!$contest) return; 

		$xp = $contest->getXPath() ;
		
		$res = $xp->query("/contest/frontend/page[@id='$page']/@href");
		$href = $res->item(0)->nodeValue ; 
		if ( substr($href,0,5) == "http:" or substr($href,0,6) =="https:") 
			$this->_redirect($href);
		$file = config::getFilename("data/contests/" . $res->item(0)->nodeValue); 
		if ( !is_file($file) )  
			echo "Please edit '$file' to view this page.";
		else
			echo file_get_contents($file);
	}
}

<?php

/*
 * PointS, the massive point plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2017  onebone <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace onebone\tokenapi\event\account;

use onebone\tokenapi\event\TokenAPIEvent;
use onebone\tokenapi\TokenAPI;

class CreateAccountEvent extends TokenAPIEvent{
	private $username, $defaultToken;
	public static $handlerList;
	
	public function __construct(TokenAPI $plugin, $username, $defaultToken, $issuer){
		parent::__construct($plugin, $issuer);
		$this->username = $username;
		$this->defaultToken = $defaultToken;
	}
	
	public function getUsername(){
		return $this->username;
	}
	
	public function setDefaultToken($token){
		$this->defaultToken = $token;
	}
	
	public function getDefaultToken(){
		return $this->defaultToken;
	}
}

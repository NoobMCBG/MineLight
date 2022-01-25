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

namespace onebone\tokenapi\provider;


use onebone\tokenapi\TokenAPI;
use pocketmine\Player;
use pocketmine\utils\Config;

class YamlProvider implements Provider{
	/**
	 * @var Config
	 */
	private $config;

	/** @var TokenAPI */
	private $plugin;

	private $token = [];

	public function __construct(TokenAPI $plugin){
		$this->plugin = $plugin;
	}

	public function open(){
		$this->config = new Config($this->plugin->getDataFolder() . "Token.yml", Config::YAML, ["version" => 2, "token" => []]);
		$this->token = $this->config->getAll();
	}

	public function accountExists($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		return isset($this->token["token"][$player]);
	}

	public function createAccount($player, $defaultToken = 1000){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(!isset($this->token["token"][$player])){
			$this->token["token"][$player] = $defaultToken;
			return true;
		}
		return false;
	}

	public function removeAccount($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->token["token"][$player])){
			unset($this->token["token"][$player]);
			return true;
		}
		return false;
	}

	public function getToken($player){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->token["token"][$player])){
			return $this->token["token"][$player];
		}
		return false;
	}

	public function setToken($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->token["token"][$player])){
			$this->token["token"][$player] = $amount;
			$this->token["token"][$player] = round($this->token["token"][$player], 2);
			return true;
		}
		return false;
	}

	public function addToken($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->token["token"][$player])){
			$this->token["token"][$player] += $amount;
			$this->token["token"][$player] = round($this->token["token"][$player], 2);
			return true;
		}
		return false;
	}

	public function reduceToken($player, $amount){
		if($player instanceof Player){
			$player = $player->getName();
		}
		$player = strtolower($player);

		if(isset($this->token["token"][$player])){
			$this->token["token"][$player] -= $amount;
			$this->token["token"][$player] = round($this->token["token"][$player], 2);
			return true;
		}
		return false;
	}

	public function getAll(){
		return isset($this->token["token"]) ? $this->token["token"] : [];
	}

	public function save(){
		$this->config->setAll($this->token);
		$this->config->save();
	}

	public function close(){
		$this->save();
	}

	public function getName(){
		return "Yaml";
	}
}

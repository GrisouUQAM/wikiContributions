<?php

	class MWJsonDecoder {
	
		/*
			Retourne les contributeurs d'un article
			
			Limit: Retourne au maximun 500 auteurs
		*/
		public static function getUserArray($json){
			return MWJsonDecoder::getArray(MWJsonDecoder::jsonDecodeRevision($json), "user");
		}
		
		/*
			Retourne les ids de revision
		*/
		public static function getRevisionArray($json){
			return MWJsonDecoder::getArray(MWJsonDecoder::jsonDecodeRevision($json), "revid");
		}
		
		/*
			Retourne les ids de sections
		*/
		public static function getSectionArray($json){
			$decoded = json_decode($json, true);
			$decoded = $decoded['mobileview']['sections'];
			
			return MWJsonDecoder::getArray($decoded, 'id');
		}
	
		/*
			Retourne les arrays contenu dans la section 'revisions' du JSON
		*/
		private static function jsonDecodeRevision($json){
			$result = json_decode($json, true);
			return $result['query']['pages'][MWJsonDecoder::getPageId($result)]['revisions'];
		}
		
		
		/*
			Retourne un array selon la valeur cle voulue
		*/
		private static function getArray($json, $key){
			$array = array();
			
			foreach ($json as $j){
				if (!in_array($j[$key], $array)){
					array_push($array,($j[$key]));
				}
			}
			
			return $array;
		}
	
		/*
			Retourne le id d'une page. Requis pour le decodage des JSON
		*/
		private static function getPageId($decodedJson){
			return key($decodedJson['query']['pages']);
		}
		
		/*
			Decode et retourne le id d'une page.
		*/
		public static function decodePageId($json){
			$result = json_decode($json, true);
			return key($result['query']['pages']);
		}
		
		/*
			
		*/
		
	}

?> 
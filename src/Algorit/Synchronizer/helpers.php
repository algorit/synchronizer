<?php

if( ! function_exists('array_alias_key'))
{
	function array_alias_key(array $input, array $alias)
	{
		$combined = array();

		foreach($input as $key => $value)
		{
			if(is_array($value) and is_array($alias[$key]) and isset($alias[$key]['name']))
			{
				$combined[$alias[$key]['name']] = array_alias_key($value, $alias[$key]['aliases']);

			}
			elseif(isset($alias[$key]) and $value !== null)
			{
				$combined[$alias[$key]] = $value;
			}
		}

		return $combined;
	}
}
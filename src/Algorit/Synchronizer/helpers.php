<?php

if( ! function_exists('array_alias_key'))
{
	/**
	 * Replace the array keys with aliases
	 *
	 * @param  array  $input
	 * @param  array  $alias
	 * @return array
	 */
	function array_alias_key(Array $input, Array $alias)
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
<?php
namespace Lib;

/**
 * Helper
 * 
 * Вспомогательные функции
 *
 * @author i.spirin
 */
class Helper
{

	/**
	 * @param string $firstName
	 * @param string $lastName
	 * @return string
	 */
	public static function makeAvatarText(string $firstName, string $lastName): string
	{
		return mb_strtoupper(mb_substr($firstName, 0, 1, 'UTF8'), 'UTF8') .
			mb_strtoupper(mb_substr($lastName, 0, 1, 'UTF8'), 'UTF8');
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public static function safeText(string $text, $autoTrim = true): string
	{
		return $text ? htmlspecialchars(strip_tags($autoTrim ? self::trim($text) : $text)) : '';
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public static function trim(string $text): string
	{
		return preg_replace('/^\s*(\S[\s\S]*\S)\s*$/m', '\\1', preg_replace('/(\p{Cf})/mu', '', $text));
	}
}

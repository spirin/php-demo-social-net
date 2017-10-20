<?php

namespace DemoSocial;

/**
 * StringHelper
 * 
 * Вспомогательные тектовые функции
 *
 * @author i.spirin
 */
class StringHelper
{

	/**
	 * Две первые буквы имени для аватарки
	 * 
	 * @param string $firstName
	 * @param string $lastName
	 * @return string
	 */
	public static function makeAvatarText($firstName, $lastName)
	{
		return mb_strtoupper(mb_substr($firstName, 0, 1, 'UTF8'), 'UTF8') .
			mb_strtoupper(mb_substr($lastName, 0, 1, 'UTF8'), 'UTF8');
	}

	/**
	 * Возвращает безопасную строку
	 *
	 * @param string $text
	 * @return string
	 */
	public static function safeText($text, $autoTrim = true)
	{
		return $text ? htmlspecialchars(strip_tags($autoTrim ? self::trim($text) : $text)) : '';
	}

	/**
	 * Обрезает символы пробелов и переносов строк по краям текста с учетом UTF-8
	 * Также убивает служебные символы
	 *
	 * @param string $text
	 * @return string
	 */
	public static function trim($text)
	{
		return preg_replace('/^\s*(\S[\s\S]*\S)\s*$/m', '\\1', preg_replace('/(\p{Cf})/mu', '', $text));
	}

}

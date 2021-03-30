<?php
namespace View;

/**
 * FooterView
 *
 * @author i.spirin
 */
class FooterView extends View
{
	protected string $template = 'footer';

	public function render(bool $toString = false): ?string
	{
		$this->data['scripts'] = static::$globalScripts;
		return parent::render($toString);
	}
}

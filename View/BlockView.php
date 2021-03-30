<?php
namespace View;

/**
 * BlockView
 *
 * @author i.spirin
 */
class BlockView extends View
{

	public function __construct(string $template, array $data = [])
	{
		$this->template = $template;
		$this->data = $data;
	}
}

<?php

namespace Lib\Response;

use \View\View;

/**
 * ViewResponse
 *
 * @author i.spirin
 */
class ViewResponse extends Response
{
	protected View $view;

	public function __construct(View $view)
	{
		$this->view = $view;
	}
	
	public function output(): void
	{
		$this->view->render(false);
	}
}

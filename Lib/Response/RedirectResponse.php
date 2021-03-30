<?php
namespace Lib\Response;

/**
 * RedirectResponse
 *
 * @author i.spirin
 */
class RedirectResponse extends Response
{

	protected string $url;

	public function __construct(string $url)
	{
		$this->url = $url;
	}

	public function output(): void
	{
		header('Location: ' . $this->url);
	}
}

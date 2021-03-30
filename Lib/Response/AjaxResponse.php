<?php
namespace Lib\Response;

/**
 * AjaxResponse
 *
 * @author i.spirin
 */
class AjaxResponse extends Response
{

	protected bool $success;
	protected string $errorMessage;
	protected $data;

	public function __construct(bool $success = true, $data = null, string $errorMessage = '')
	{
		$this->success = $success;
		$this->errorMessage = $errorMessage;
		$this->data = $data;
	}

	public function output(): void
	{
		echo \json_encode([
			'success' => $this->success,
			'error' => $this->success ? '' : $this->errorMessage,
			'data' => $this->data,
		]);
	}
}

<?php
namespace View;

/**
 * View
 *
 * @author i.spirin
 */
class View
{

	/**
	 * Корневой каталог темы
	 * 
	 * @var string
	 */
	protected string $themePath = '/templates/default';

	/**
	 * Путь к шаблону от корня темы без расширения файла
	 * 
	 * @var string
	 */
	protected string $template = '';

	/**
	 * Данные для передачи в шаблон
	 * 
	 * @var array
	 */
	protected array $data = [];

	/**
	 * Сборка inline скриптов для последующего размещения в футере 
	 * 
	 * @var array
	 */
	public static array $globalScripts = [];

	public function render(bool $toString = false): ?string
	{
		extract($this->data);
		if ($toString) {
			ob_start();
		}
		require sprintf('%s%s/%s.php', SERVER_ROOT, $this->themePath, $this->template);
		if ($toString) {
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return null;
	}

	public function renderBlock(string $template, array $data = [], bool $toString = false): ?string
	{
		$view = new View();
		$view->template = $template;
		$view->data = $data;
		return $view->render($toString);
	}

	public function scriptStart(): void
	{
		ob_start();
	}

	public function scriptEnd(): void
	{
		$contents = ob_get_contents();
		ob_end_clean();
		static::$globalScripts[] = $contents;
	}

	public function __toString(): string
	{
		return $this->render(true);
	}
}

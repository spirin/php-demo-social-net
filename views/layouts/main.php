<?php
$this->renderBlock('header');
?>





<div class="container">


	<nav class="navbar navbar-expand-lg navbar-light bg-light" style="margin-top: 10px;margin-bottom: 20px">
		<a class="navbar-brand" href="/">DemoSocialNet</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">

			</ul>
			<?php if ($userIsAuthorized && $controllerRoute !== 'search'): ?>
				<form class="form-inline my-2 my-lg-0" action="/" method="get">
					<input class="form-control mr-sm-2" type="text" placeholder="Поиск людей" aria-label="Найти" name="query">
					<input type="hidden" name="route" value="search" />
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Найти</button>
				</form>
			<?php endif; ?>
		</div>
	</nav>


	<div class="row">
		<div class="col-3">
			<?php if ($userIsAuthorized): ?>

				<?php $this->renderBlock('userbar'); ?>

			<?php endif; ?>
		</div>
		<div class="col">
			<?php
			echo $content;
			?>
		</div>
	</div>


</div>
<?php
$this->renderBlock('footer');

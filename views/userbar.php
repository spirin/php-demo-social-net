<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-pills flex-column">
				<li class="nav-item">
					<a class="nav-link" href="'/?route=wall&id=<?php echo $sessionUser['id']; ?>" role="tab">Моя стена</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/?route=friends">Мои друзья<?php if ($sessionNewFriends) : 
						?> <span class="badge badge-info"><?php echo $sessionNewFriends; ?></span><?php endif; ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/?route=search">Поиск людей</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/?route=profile">Профиль</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/?route=logout">Выход</a>
				</li>
			</ul>
		</div>
	</div>
</div>
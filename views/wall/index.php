<div class="container-fluid">
	<div class="row">
		<div class="col-2">
			<div class="rounded-circle avatar-big"><?php
				echo \DemoSocial\StringHelper::makeAvatarText($wallUser['firstname'], $wallUser['lastname']);
				?></div>
		</div>
		<div class="col-10">
			<h3 style="text-align: left"><?php echo $wallUser['firstname']; ?> <?php echo $wallUser['lastname']; ?></h3>
			<p><?php echo (int) $wallUser['sex'] ? 'Мужчина' : 'Женщина'; ?>, 
				<?php echo DateTime::createFromFormat('Y-m-d H:i:s', $wallUser['borndate'])->format('d.m.Y') ?></p>
			<p><?php echo $wallUser['about']; ?></p>

			<br />
			<?php if ($showFriendOfferNew) : ?>
				<a class="btn btn-warning ajaxAction" href="/?route=ajaxRequest&id=<?php echo $wallUser['id']; ?>">Подружиться</a>
				<?php $this->scriptStart(); ?>
				<script type="text/javascript">
					addAjaxAction('.ajaxAction', function () {
						location = location;
					});
				</script>
				<?php $this->scriptEnd(); ?>
			<?php elseif ($showFriendOfferWait) : ?>
				<div class="alert alert-warning" role="alert">
					Пользователь внимательно рассматривает предложение подружиться, подождите.
				</div>
			<?php elseif ($showFriendOfferRejected) : ?>
				<div class="alert alert-danger" role="alert">
					Пользователь отклонил предложение подружиться :(
				</div>
			<?php endif; ?>
			<?php if ($showMessageNew) : ?>
				<a class="btn btn-warning ajaxNewMessage" href="/?route=ajaxComment&target=wp&targetId=<?php echo $sessionUser['id']; ?>&treeId=<?php echo $sessionUser['id']; ?>">Добавить сообщение на стену</a>
			<?php endif; ?>
			<?php $this->scriptStart(); ?>
			<script type="text/javascript">
				$('.ajaxNewMessage').click(function () {
					let link = $(this).attr('href');

					showModalMessage(
							'<div class="form-group"><textarea class="form-control commentContent" rows="5"></textarea></div>',
							'Новое сообщение',
							[{
									type: 'primary',
									text: 'Отправить',
									callback: function (e) {
										$.getJSON(link, {content: e.modal.find('.commentContent').val()}, function (response) {
											if (response) {
												if (response.success) {
													location = location;
												} else {
													alert(response.error);
												}
											} else {
												alert('Ошибка соединения с сервером');
											}
										});
									}
								}]);

					return false;
				});
			</script>
			<?php $this->scriptEnd(); ?>
		</div>
	</div>

	<hr />
	<div id="results" class="container-fluid">

	</div>

	<script id="templateResultRow" style="display: none" type="x-template">
		<div class="row border-bottom border-secondary" style="margin-bottom: 10px">
		<div class="col-1">
		<div class="rounded-circle avatar-small"><%= firstname.toUpperCase().charAt(0) %><%= lastname.toUpperCase().charAt(0) %></div>
		</div>
		<div class="col-11">

		<div class="border border-top-0 border-right-0 border-left-0 border-primary" style="margin-bottom: 10px">
		<div><a href="/?route=wall&id=<%- id %>"><%- firstname %> <%- lastname %></a></div>
		<div class="text-muted"><%- date %></div>
		<div><%- content %></div>
		</div>

		</div>
		</div>
	</script>
	<script id="templateShowMore" style="display: none" type="x-template">
		<button id="showmore" class="btn btn-outline-default" style="width:100%">Показать еще</button>
	</script>
	<script id="templateAjaxLoader" style="display: none" type="x-template">
		<p class="text-center" id="progressBar"><img src="/img/ajax-loader.gif" alt="." /></p>
	</script>

	<?php $this->scriptStart(); ?>

	<script type="text/javascript">
		let templateResultRow = _.template($('#templateResultRow').text());
		let templateShowMore = _.template($('#templateShowMore').text());
		let templateAjaxLoader = _.template($('#templateAjaxLoader').text());
		let globalPage = 0;

		function appendResultRow(data, prepend) {
			if (prepend) {
				$('#results').prepend(templateResultRow(data));
			} else {
				$('#results').append(templateResultRow(data));
			}
		}
		function appendShowMoreButton() {
			$('#results').append(templateShowMore());
			$('#showmore').click(function () {
				appendResults(30);
			});
		}
		function appendAjaxLoader() {
			$('#results').append(templateAjaxLoader());
		}

		function appendResults(limit) {
			let container = $('#results');

			container.find('#showmore').remove();
			appendAjaxLoader();

			$.getJSON('/?route=ajaxGetComments&target=wp&targetId=<?php echo $wallUser['id']; ?>&treeId=<?php echo $wallUser['id']; ?>&page=' + globalPage, {}, function (response) {
				container.find('#progressBar').remove();

				if (response) {
					if (response.success) {
						if (results = response.data) {
							for (let i in results) {
								appendResultRow(results[i]);
							}
							globalPage++;
							if (results.length === limit) {
								appendShowMoreButton();
							}
						}
					} else {
						alert(response.error);
					}
				} else {
					alert('Ошибка соединения с сервером');
				}
			});
		}

		appendResults(30);
	</script>
	<?php $this->scriptEnd(); ?>
</div>
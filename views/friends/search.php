<h3>Поиск людей</h3>

<br />

<form>
	<div class="row">
		<div class="col-10">
			<input type="text" class="form-control" id="searchQuery" placeholder="Имя или фамилия ..." value="<?php echo htmlspecialchars(@$_GET['query']) ?>">
		</div>
		<div class="col-2">
			<button class="btn btn-outline-info" type="submit" id="submit">Найти</button>
		</div>
	</div>
</form>

<hr />

<div id="results">

</div>

<script id="templateResultRow" style="display: none" type="x-template">
	<div class="row border-bottom border-secondary" style="margin-bottom: 10px">
	<div class="col-1">
	<div class="rounded-circle avatar-small"><%= firstname.toUpperCase().charAt(0) %><%= lastname.toUpperCase().charAt(0) %></div>
	</div>
	<div class="col-11">
	<a href="/?route=wall&id=<%- id %>"><%- firstname %> <%- lastname %></a>
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

	function appendResultRow(data) {
		$('#results').append(templateResultRow(data));
	}
	function appendShowMoreButton() {
		$('#results').append(templateShowMore());
	}
	function appendAjaxLoader() {
		$('#results').append(templateAjaxLoader());
	}

	function appendResults(query, page, limit) {
		let container = $('#results');

		container.find('#showmore').remove();
		appendAjaxLoader();

		$.getJSON('/?route=ajaxSearch&query=' + query + '&page=' + page, {}, function (response) {
			container.find('#progressBar').remove();

			if (response) {
				if (response.success) {
					if (results = response.data) {
						for (let i in results) {
							appendResultRow(results[i]);
						}
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

	$('#submit').click(function () {
		$('#results').html('');

		appendResults($('#searchQuery').val(), 0, 30);

		return false;
	});

	$('#submit').click();
</script>
<?php
$this->scriptEnd();

<h3>Мои друзья</h3>

<br />

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link" href="#all" role="tab">Все друзья</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#confirmed" role="tab">Подтвержденные</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#requested" role="tab">Хотят подружиться</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#rejected" role="tab">Отклоненные</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#my" role="tab">Мои запросы</a>
	</li>
</ul>

<br />

<div id="results" class="container-fluid">

</div>


<script id="templateResultRow" style="display: none" type="x-template">
	<div class="row border-bottom border-secondary" style="margin-bottom: 10px" id="offerRow<%- id %>">
	<div class="col-1">
	<div class="rounded-circle avatar-small"><%= (''+firstname).toUpperCase().charAt(0) %><%= (''+lastname).toUpperCase().charAt(0) %></div>
	</div>
	<div class="col-7">
	<a href="/?route=wall&id=<%- uid %>"><%- firstname %> <%- lastname %></a>
	</div>
	<div class="col-4 text-right">
		<% if (tabName !== 'all'){ %>
			<% if (tabName === 'requested' || tabName === 'rejected'){ %>
			<a href="/?route=ajaxConfirm&id=<%- id %>" class="btn btn-success btn-sm" id="confirmBtn<%- id %>">Подтвердить</a>
			<% } 
			   if (tabName === 'requested' || tabName === 'confirmed'){ %>
			<a href="/?route=ajaxReject&id=<%- id %>" class="btn btn-warning btn-sm" id="rejectBtn<%- id %>">Отклонить</a>
			<% } 
			   if (tabName === 'my'){ %>
					<% if (status === 'nw'){ %>
					<span class="badge badge-secondary">Пользователь еще не решил</span>
					<% } else if (status === 'cd'){ %>
					<span class="badge badge-success">Пользователь подтвердил заявку</span>
					<% } else if (status === 'rd'){ %>
					<span class="badge badge-danger">Пользователь отклонил заявку</span>
				<% } %>	
			<% } %>
		<% } %>
	</div>
	</div>
</script>
<script id="templateShowMore" style="display: none" type="x-template">
	<button id="showmore" class="btn btn-outline-default" style="width:100%">Показать еще</button>
</script>
<script id="templateAjaxLoader" style="display: none" type="x-template">
	<p class="text-center" id="progressBar"><img src="/img/ajax-loader.gif" alt="." /></p>
</script>
<script id="templateNoEntries" style="display: none" type="x-template">
	<p class="emptyDataNotice">Нет заявок</p>
</script>
<script id="templateNoEntriesMy" style="display: none" type="x-template">
	<p class="emptyDataNotice">Вы еще не предлагали подружиться, поищите новых друзей в <a href="/?route=search">поиске</a></p>
</script>

<?php $this->scriptStart(); ?>
<script type="text/javascript">
	let templateResultRow = _.template($('#templateResultRow').text());
	let templateShowMore = _.template($('#templateShowMore').text());
	let templateAjaxLoader = _.template($('#templateAjaxLoader').text());
	let templateNoEntries = _.template($('#templateNoEntries').text());
	let templateNoEntriesMy = _.template($('#templateNoEntriesMy').text());

	function appendResultRow(data) {
		$('#results').append(templateResultRow(data));
		
		let offerId = data.id;
		addAjaxAction('#confirmBtn' + offerId, function () {
			$('#offerRow' + offerId).remove();
		});
		addAjaxAction('#rejectBtn' + offerId, function () {
			$('#offerRow' + offerId).remove();
		});
	}
	function appendShowMoreButton() {
		$('#results').append(templateShowMore());
	}
	function appendAjaxLoader() {
		$('#results').append(templateAjaxLoader());
	}
	function appendNoEntries(tabName) {
		$('#results').append(tabName !== 'my' ? templateNoEntries() : templateNoEntriesMy());
	}

	function appendResults(tabName, page, limit) {
		let container = $('#results');

		container.find('#showmore').remove();
		appendAjaxLoader();

		$.getJSON('/?route=ajaxOffers&group=' + tabName + '&page=' + page, {}, function (response) {
			container.find('#progressBar').remove();

			if (response) {
				if (response.success) {
					if (results = response.data) {
						for (let i in results) {
							results[i].tabName = tabName;
							appendResultRow(results[i]);
						}
						if (results.length === limit) {
							appendShowMoreButton();
						}
						if (results.length === 0) {
							if (results.length === 0) {
								appendNoEntries(tabName);
							}
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

	$('#myTab a').click(function (e) {
		e.preventDefault();
		let tabName = e.target.href.split('#')[1];
		$(this).tab('show');

		$('#results').html('');
		appendResults(tabName, 0, 30);
	});

	$('#myTab a:first').click();
</script>
<?php $this->scriptEnd(); ?>